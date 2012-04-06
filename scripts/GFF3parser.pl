#!/usr/bin/perl
use strict;
use warnings;
use Getopt::Std;

## Usage: GFF3parser.pl -n <name> -i <gff3_file> -c <configuration_file> -o <output_file>

my %options=();
getopts("n:i:c:o:", \%options);
if (!defined $options{n} || !defined $options{i} || !defined $options{c} || !defined $options{o}) {
	error("Usage: gff3parser.pl -n <name> -i <gff3_file> -c <configuration_file> -o <output_file>");
}

## Open the configuration file
open(CONF, $options{c}) or error("ERROR: Configuration File conf_file.txt does not exist");
## load the lines
my @lines = <CONF>;
close CONF;

## initialize the array for the selected features from configuration file
my @selected;
## initialize an hash to store the features
my %hash_conf;
## Go thru the Configuration file to extract information on features
foreach my $line (@lines){
	chomp($line);
	## get all the items
	my @items = split(/\t/, $line);
	## Error if the file is not in GFF3 format
	error("ERROR: Configuration file is not 3 columns separated by tabs") if (scalar @items != 3);
	## load the hash, with shape and color information for each feature
	$hash_conf{$items[0]}{'shape'} = $items[1];
	$hash_conf{$items[0]}{'color'} = $items[2];
	## store the features in array
	push(@selected, $items[0]);
}

## Open the GFF3 file
open(FILE, $options{i}) or error("GFF3 does not exist");
## Output file
open(OUT, ">", $options{o});
## For each line in GFF3 file
foreach my $line (<FILE>){
	## terminate at the FASTA information
	last if $line =~ /##.*FASTA/;
	## Skip the initial information
	next if ($line =~ /^#/);
	## split each line and authenticate the GFF3 format
	my @tabs = split(/\t/, $line);
	error("ERROR: GFF3 file is not 9 columns separated by tabs") if (scalar @tabs != 9);
	## If the feature is present in the configuration
	if ( grep {$_ eq $tabs[2]} @selected ){
		## generate annotation line
		print OUT $options{n}.get_line( @tabs);
	}
}
close FILE;
close OUT;
exit;
########################################################################

## Generate the annotation line
sub get_line {
	my @tabs = @_;
	my $name = get_locus($tabs[8]);
	my $ann = "\t$tabs[3]\t$tabs[4]\t$tabs[6]\t$name\t";
	if ($tabs[2] eq "microarray_oligo"){
		$ann .= $tabs[5];
	} else { 
		$ann .= ".";
	}
	$ann .= "\t$tabs[2]\t".$hash_conf{$tabs[2]}{'shape'}."\t".$hash_conf{$tabs[2]}{'color'}."\n";
	return $ann;
}

## Get the feature name
sub get_locus{
	my $nine = shift;
	chomp($nine);
	my $term = ".";
	my @info = split(/;/, $nine);
	foreach(@info){
		next if ($_ !~ /locus_tag/);
		$term = $_;
		$term =~ s/locus_tag=//g;
	}
	return $term;
}

## Print out error message and terminate the command
sub error{
	my $msg = shift;
	print $msg,"\n";
	exit;	
}

