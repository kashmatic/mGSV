#!/usr/bin/perl
use strict;
use warnings;
use Getopt::Std;

## Usage: BLASTparser.pl -r <query_genome> -t <hit_genome> -i <blastz__output_file> -o <output_file>

my %options=();
getopts("r:t:i:o:", \%options);
if (!defined $options{r} || !defined $options{t} || !defined $options{i} || !defined $options{o}) {
	error("Usage: BLASTparser.pl -n <name> -i <gff3_file> -c <configuration_file> -o <output_file>");
}

$/="}
a {";

## Open the BLASTZ output file
open(FILE, $options{i}) or error("GFF3 does not exist");
## Output file
open(OUT, ">", $options{o});
print OUT "#Ref\tref_start\tred_end\tTile\ttile_start\ttile_end\tscore\n";
## For each line in blastz output file
<FILE>;
foreach my $line (<FILE>){
	my @set = split(/\n/, $line);
	## Extract relevent information
	my ($score) = $set[1] =~ /(\d+)/;
	my ($q_start, $h_start) = $set[2] =~ /(\d+)\ (\d+)/;
	my ($q_end, $h_end) = $set[3] =~ /(\d+)\ (\d+)/;
	print OUT $options{r}."\t$q_start\t$q_end\t".$options{t}."\t$h_start\t$h_end\t$score\n";
}
close FILE;
close OUT;
exit;

########################################################################

## Print out error message and terminate the command
sub error{
	my $msg = shift;
	print $msg,"\n";
	exit;	
}

