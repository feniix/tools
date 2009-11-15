#!/usr/bin/perl

################################################################################
## md5 and sha1 generator for pom and jar files
##
## Author: 
##	Lóránd Somogyi (http://lorands.com)
##
## Distributd under Apache License v2
## 	http://www.apache.org/licenses/LICENSE-2.0.txt
## 
## Requirements:
##	* OS: Linux*
##	* Perl 5+
##
## Usage:
##	1. cd to root of maven2 repository directory
##	2. execute the script
##
## Output:
##	creates (and overwrites) md5 sums for POMs and JARs
##
################################################################################

my @files = `find . -name *.pom`;

genSums(@files);

@files = `find . -name *.jar`;

genSums(@files);

sub genSums {
	foreach my $line (@_) {
		chomp $line;
		`sha1sum $line | awk '{print \$1}' > $line.sha1`;
		`md5sum $line | awk '{print \$1}' > $line.md5`;
	
	}
}
