/*
 * $Id: sslcat.c,v 1.1.1.1 2006/03/21 23:20:38 dave Exp $
 */
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/uio.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <err.h>
#include <errno.h>
#include <fcntl.h>
#include <netdb.h>
#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sysexits.h>
#include <unistd.h>

#include <openssl/crypto.h>
#include <openssl/x509.h>
#include <openssl/pem.h>
#include <openssl/ssl.h>
#include <openssl/err.h>

#include "sslcat.h"

int	debugging = 0;
int	drop_on_eof = 0;
int	SSLv3 = 0;
int	TLSv1 = 0;
int	hex = 0;

void
error (int exit_code, const char *fmt, ...)
{
	va_list	 args;

	va_start(args, fmt);
	fprintf(stderr, "ERROR: ");
	vfprintf(stderr, fmt, args);
	fprintf(stderr, "\n");
	va_end(args);
	exit(exit_code);
}

void
debug (char *fmt, ...)
{
	va_list	 args;

	if (!debugging) return;
	va_start(args, fmt);
	vfprintf(stderr, fmt, args);
	fprintf(stderr, "\n");
	va_end(args);
}

void hexdump (void *src, int len) {
	unsigned char	*cp = (char *)src;
	char		hex[128], ascii[32];
	int		i;

	for (i = 0; i < len; i++) {
		if (!(i % 16)) {
			if (i) printf("%-54s %s\n", hex, ascii);
			sprintf(hex, "%04x: ", i);
			strcpy(ascii, "................");
		}
		sprintf((hex + 6) + (3 * (i % 16)), "%02x ", cp[i]);
		if (cp[i] > 32 && cp[i] < 128)
			ascii[i % 16] = cp[i];
	}
	printf("%-54s %s\n", hex, ascii);
}

char *
ssl_err_message (SSL *ssl, int code) {
	static char	buffer[1024];
	int		error;

	error = SSL_get_error(ssl, code);
	switch (error) {
	case SSL_ERROR_NONE:
		return("No error occured");
	case SSL_ERROR_SSL:
		error = ERR_get_error();
		return(strcpy(buffer, ERR_reason_error_string(error)));
	case SSL_ERROR_WANT_READ:
		return("SSL Want Read");
	case SSL_ERROR_WANT_WRITE:
		return("SSL Want Write");
	case SSL_ERROR_WANT_X509_LOOKUP:
		return("SSL Want X509 Lookup");
	case SSL_ERROR_SYSCALL:
		error = ERR_get_error();
		if (error == 0) {
			switch(code) {
			case -1: return(strerror(errno));
			case 0:	 return("Unexpected EOF");
			default: return("Unknown Internal Error");
			}
		} else
			return(strcpy(buffer, ERR_reason_error_string(error)));
	case SSL_ERROR_ZERO_RETURN:
		return("SSL Zero Return");
	case SSL_ERROR_WANT_CONNECT:
		return("SSL Want Connect");
	}

	sprintf(buffer, "Unknown error (code=%d, error=%d)", code, error);
	return(buffer);
}

void
usage (char *exe)
{
	fprintf(stderr, "sslcat - version %s\n", VERSION);
	fprintf(stderr, "Usage: %s [options] <host> <port>\n\n", exe);
	fprintf(stderr, "Options:\n");
	fprintf(stderr, "    -B  Enable debugging\n");
	fprintf(stderr, "    -d  Drop connection on EOF from STDIN\n");
	fprintf(stderr, "    -3  Use SSL version 3 (Default is SSLv2)\n");
	fprintf(stderr, "    -T  Use TLS version 1 (Default is SSLv2)\n");
	fprintf(stderr, "    -x  Enable hexadecimal output.\n");
}

int
main (int argc, char *argv[])
{
	char			*exename = argv[0];
	int			sock, port, i;
	int			stdin_bytes = 1;
	char			buffer[BUFFER_SIZE];
	fd_set			fds;
	struct hostent		*host;
	struct sockaddr_in	sa;
	SSL_METHOD		*meth;
	SSL_CTX			*ctx;
	SSL			*ssl;

	while ((i = getopt(argc, argv, "Bced3Tx")) != -1) {
		switch(i) {
		case 'B':
			debugging = 1;
			debug("Debugging enabled");
			break;
		case 'd':
			drop_on_eof = 1;
			debug("Drop on EOF enabled");
			break;
		case '3':
			SSLv3 = 1;
			debug("SSLv3 enabled");
			break;
		case 'T':
			TLSv1 = 1;
			debug("TLSv1 enabled");
			break;
		case 'x':
			hex = 1;
			debug("Hex output enabled");
			break;
		default:
			exit(EX_USAGE);
			/* NOTREACHED */
		}
	}

	if (SSLv3 && TLSv1)
		error(EX_USAGE, "Can't use SSLv3 and TLSv1 at the same time!");

	argc -= optind;
	argv += optind;

	if ((argc) < 1) {
		usage(exename);
		exit(EX_USAGE);
	}

	if ((i = inet_aton(argv[0], &sa.sin_addr)) != 1) {
		if (i < 0)
			err(EX_OSERR, "inet_aton() failed");
		debug("Resolving hostname");
		if ((host = gethostbyname(argv[0])) == NULL)
			errx(EX_NOHOST, hstrerror(h_errno));
		memcpy(&sa.sin_addr, (struct in_addr *)host->h_addr,
		    sizeof(sa.sin_addr));
	}

	if (argc > 1) {
		if ((port = atoi(argv[1])) == 0)
			error(EX_USAGE, "Invalid port specified");
	} else {
		port = 443;
	}

	debug("Initialising SSL");
	SSLeay_add_ssl_algorithms();
	SSL_load_error_strings();

	debug("Getting SSL method");
	if (SSLv3)
		meth = SSLv3_client_method();
	else
		if (TLSv1)
			meth = TLSv1_client_method();
		else
			meth = SSLv2_client_method();
	if (meth == NULL)
		error(EX_IOERR, "Unable to create method - %s",
		    ERR_reason_error_string(ERR_get_error()));

	debug("Creating SSL context");
	if ((ctx = SSL_CTX_new(meth)) == NULL)
		error(EX_IOERR, "Unable to create context - %s",
		    ERR_reason_error_string(ERR_get_error()));

	debug("Opening socket");
	if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0)
		error(EX_IOERR, "socket() - %s", strerror(errno));

	sa.sin_family = AF_INET;
	sa.sin_port = htons(port);
	/* sa.sin_addr completed above */
	memset(&(sa.sin_zero), 0, 8);

	debug("Connecting socket to %s:%d",
	    inet_ntoa(sa.sin_addr), port);
	if (connect(sock, (struct sockaddr *)&sa, sizeof(sa)) < 0)
		error(EX_IOERR, "connect() - %s", strerror(errno));

	debug("Creating SSL instance");
	if ((ssl = SSL_new(ctx)) == NULL)
		error(EX_IOERR, "ssl() - %s",
		    ERR_reason_error_string(ERR_get_error()));

	debug("Negotiating SSL session");
	SSL_set_fd(ssl, sock);
	if ((i = SSL_connect(ssl)) == -1)
		error(EX_IOERR, "SSL_connect() - %s",
		    ssl_err_message(ssl, i));

	debug("Entering select loop");
	while(1) {
		FD_ZERO(&fds);
		if (stdin_bytes > 0)
			FD_SET(0, &fds);
		FD_SET(sock, &fds);

		if ((i = select(sock+1, &fds, NULL, NULL, NULL)) < 0)
			error(EX_IOERR, "select() - %s", strerror(errno));

		if (FD_ISSET(0, &fds)) {
			if ((stdin_bytes = read(0, buffer, BUFFER_SIZE)) == 0) {
				if (drop_on_eof) break;
					else continue;
			}

			debug("Read %d bytes from stdin", stdin_bytes);
			if ((i = SSL_write(ssl, buffer, stdin_bytes)) == 0)
				error(EX_IOERR, "SSL_write wrote 0 bytes!");
			debug("Write %d bytes to socket", i);
		}
		if (FD_ISSET(sock, &fds)) {
			if ((i = SSL_read(ssl, buffer, BUFFER_SIZE)) <= 0)
				break;
			debug("Read %d bytes from socket", i);
			if (hex == 0) {
				i = write(1, buffer, i);
				debug("Wrote %d bytes to stdout", i);
			} else
				hexdump(buffer, i);
		}
	}

	debug("Shutting down ssl connectiomn");
	SSL_shutdown(ssl);

	debug("Closing socket");
	close(sock);

	debug("Shutting down ssl");
	SSL_free(ssl);
	SSL_CTX_free(ctx);

	return(EX_OK);
}
