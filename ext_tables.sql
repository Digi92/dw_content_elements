#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	colPos int(11) DEFAULT '0' NOT NULL,
	foreign_uid int(11) DEFAULT '0' NOT NULL,
	parent_table varchar(255) DEFAULT '' NOT NULL,
);
