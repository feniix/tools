#!/usr/bin/perl

################################################################################
## maven-metadata.xml generator.
##
## Author: 
##	Lóránd Somogyi (http://lorands.com)
##
## Distributd under Apache License v2
## 	http://www.apache.org/licenses/LICENSE-2.0.txt
## 
## Requirements:
##	* OS: Linux*
##	* Perl 5+ with XML::Simple
##
## Usage:
##	1. cd to root of maven2 repository directory
##	2. execute the script
##
## Note:
##	This script ignores SNAPSHOTS
##
## Output:
##	1. creates (and overwrites) maven-metadata.xml
##	2. creates (and overwrites) maven-metadata.xml.sha1
##
################################################################################

use XML::Simple;
use List::Util qw[min max];

submit();

sub submit {
	@poms = `find . -name *.pom | sort`;
	
	my $lastGA = "";
	my @gaVersions = ();
	my $lastGroupId;
	my $lastArtifactId;
	my $lastVersion;
	foreach my $line (@poms) {
		my @parts = split /\//, $line;
		#my $size =  scalar (@parts);
		
		my $pomFile = pop(@parts);
		
		chomp $line;
		my ($groupId, $artifactId, $version) = getGAV($line);
		#print "$groupId:$artifactId:$version\n";
		
		my $ga = "$groupId:$artifactId";
		#print "$ga  -- $lastGA\n";
		
		if( $lastVersion ne "" ) {
			push @gaVersions, $lastVersion;
		}

		#print "$ga : $version\n";
		if( $lastGA eq $ga ) {
			#print "@gaVersions, $version\n";
			#push @gaVersions, $version;
			#$gaVersions[++$#gaVersions] = $version;
		} else {
			$lastGA = $ga;
			#print "+++++++> @gaVersions \n";
			if( ($lastGA ne "") && ($groupId ne "") ) { 
				#print "===> $lastGroupId, $lastArtifactId, @gaVersions \n";

				writeMeta($lastGroupId, $lastArtifactId, @gaVersions);
			}
			##empty versions
			@gaVersions = (); 
		}
		$lastGroupId = $groupId;
		$lastArtifactId = $artifactId;
		$lastVersion = $version;
	}
	
	##for last element
	#print "===> $lastGroupId, $lastArtifactId, @gaVersions \n";
	#push @gaVersions, $lastVersion;
	writeMeta($lastGroupId, $lastArtifactId, @gaVersions);
	
	
}

sub writeMeta {
	my ($groupId, $artifactId, @versions) = @_;
	
	#print "@versions \n";
	my $relVersion = getMaxVersion(@versions);
	
	my $out = genMetaString($groupId, $artifactId, $relVersion, @versions);
	
	my $groupPath = $groupId;
	$groupPath =~ s/\./\//g;

	chomp $groupPath;
	chomp $artifactId;
	
	my $subPath = "$groupPath/$artifactId"; 
	my $path = "$subPath/maven-metadata.xml";
	open (MYFILE, ">$path");
	print MYFILE $out;
	close (MYFILE);
	
	#print "$path --- $out \n\n\n";
	print "written: $path \n ";

	##sha 
	my $shaPath = "$subPath/maven-metadata.xml.sha1";
	`sha1sum $path | awk '{print \$1}' > $shaPath`;
	print "SHA1 written to $shaPath\n";

}

sub getMaxVersion {
	@relAry = removeSnapthots( @_);
	
	#print "@relAry \n";
	
	my @versions = sort { verCmp($a, $b) } @relAry;	
	
	#$max = pop @versions;
	$max = $versions[0];
	
	return $max;
}

sub removeSnapthots {
	my @ret;
	
	foreach my $it (@_) {
		if( $it =~  m/.*\-SNAPSHOT$/ ) {
			
		} else {
			push @ret, $it;
		}
	}
	return @ret;
}

sub verCmp {
	my ($a, $b) = @_;
	
	#print "---> cmp: $a vs $b\n";
	
	if( ($a eq "") || ($b eq "") ) {
		return 0;
	}
	
	my @aParts =  split /\./, $a;
	my @bParts =  split /\./, $b;
	
	#print "Comp>>> $a vs $b";
	
	for( my $i = 0; $i < min(scalar(@aParts), scalar(@bParts)); $i++ ) {
		#print $aParts[$i] . " vs " . $bParts[$i] . "\n";
		if( $aParts[$i] < $bParts[$i] ) {
			#print " c   LT\n";
			return 1;
		}
		if( $aParts[$i] > $bParts[$i] ) {
			#print " c   GT\n";
			return -1;
		}
	}
	#print " c   EQ\n";

	return 0;	
}

sub getGAV {
	my ($file) = @_;
	my $xs1 = XML::Simple->new();

	my $doc = $xs1->XMLin($file);
	
	my $groupId = $doc->{groupId};
	if( $groupId eq "" ) {
		$groupId = $doc->{parent}->{groupId};
	}
	my $artifactId = $doc->{artifactId};
	my $version = $doc->{version};
	if( $version eq "" ) {
		#print "---------------------------------------->";
		$version = $doc->{parent}->{version};
	}
	
	#print "$groupId:$artifactId:$version\n";

	return ($groupId, $artifactId, $version);
}


sub genMetaString {
	my ($groupId, $artifactId, $rel, @vers) = @_;
	
	#print "@vers \n";
	my $versions = " ";
	foreach my $v (@vers) {
		$versions .= "\t\t\t<version>$v</version>\n";
	}
	
	my $ts = currTs();
	return "<?xml version=\"1.0\"?>\n<metadata>\n\t<groupId>$groupId</groupId>\n\t<artifactId>$artifactId</artifactId>\n\t<version>$rel</version>\n\t<versioning>\n\t\t<release>$rel</release>\n\t\t<versions>\n$versions\t\t</versions>\n\t\t<lastUpdated>$ts</lastUpdated>\n\t</versioning>\n</metadata>";	
}

sub currTs {
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);
	return sprintf("%4d%02d%02d%02d%02d%02d", $year+1900,$mon+1,$mday,$hour,$min,$sec);
}
