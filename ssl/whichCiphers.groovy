#!/usr/bin/env groovy
/* 
Written by Chris Mahns, 2009 with an awful lot of help from this article:
http://www.java2s.com/Code/JavaAPI/javax.net.ssl/SSLSocketgetSupportedCipherSuites.htm

This script will report back the SSL cipher used to negotiate
the connection between your workstation and a remote ssl server.

You can override, with the -c (or --cipher) switch to test using a 
specific cipher.  Case is not important when setting the cipher as
it will be converted to Upper Case by the script.
*/

import javax.net.ssl.*

// This section sets up the command line arguments portion
// of this script.
def cli = new CliBuilder( usage: 'getCipher [-h "hostname"] [-p "port"] [-c "cipher"] ' )
  cli.h( longOpt:'host', args:1, required:true, type:GString, 'The host or site you want to test' )
  cli.p( longOpt:'port', args:1, required:false, type:GString, 'The ssl port.  Default is 443')
  cli.c( longOpt:'cipher', args:1, required:false, type:GString, 'Optionally, test with a specific cipher')

def opt = cli.parse(args)
  if (!opt) return
  if (opt.h) host = opt.h
  if (opt.c) cipher = (opt.c).toUpperCase()

def port = 443
  if (opt.p) port = Integer.parseInt(opt.p)

// Create the socket
def factory = SSLSocketFactory.getDefault() 
def socket = factory.createSocket("$host", port)
  socket.getEnabledCipherSuites()
  if (opt.c) {
    socket.setEnabledCipherSuites(cipher) 
    }
    else {
  }

socket.addHandshakeCompletedListener( new listener() )
socket.startHandshake()

class listener implements HandshakeCompletedListener {
  void handshakeCompleted(HandshakeCompletedEvent e) {
    println('Handshake succesful!')
    println('Cipher suite used: ' + e.getCipherSuite())
  }
}

socket.close()
