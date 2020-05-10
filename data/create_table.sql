
DROP TABLE IF EXISTS `$prefix_admin`;

CREATE TABLE `$prefix_admin` (
  `user_id` varbinary(200) NOT NULL default '',
  `user_pass` varbinary(200) default NULL,
  `user_email` varchar(60) default NULL,
  `add_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_login` timestamp ,
  `last_ip` varchar(15) default NULL,
  PRIMARY KEY  (`user_id`)
) DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `$prefix_admin_login`;

CREATE TABLE `$prefix_admin_login` (
  `ip` varchar(50) NOT NULL default '',
  `time_for_login` varchar(50) NOT NULL default '',
  `try_time` int(11) default NULL,
  PRIMARY KEY  (`ip`,`time_for_login`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_client`;

CREATE TABLE `$prefix_client` (
  `client_no` int(10) unsigned NOT NULL auto_increment,
  `client_company` varchar(50) default NULL,
  `client_addr` varchar(50) default NULL,
  `tax_no` varchar(100) default NULL,
  `bank_name` varchar(100) default NULL,
  `client_phone` varchar(50) default NULL,
  `add_time` timestamp NULL default NULL,
  `remark` varchar(300) default NULL,
  PRIMARY KEY  (`client_no`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_instock`;

CREATE TABLE `$prefix_instock` (
  `in_batch_id` bigint(20) NOT NULL auto_increment,
  `in_company` varchar(200) default NULL,
  `add_date` varchar(30) default NULL,
  `add_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `total_money` decimal(18,2) default NULL,
  `remark` varchar(300) default NULL,
  `summary` varchar(300) default NULL,
  PRIMARY KEY  (`in_batch_id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_instock_detail`;

CREATE TABLE `$prefix_instock_detail` (
  `detail_id` bigint(20) NOT NULL auto_increment,
  `in_batch_id` bigint(20) default NULL,
  `product_id` int(11) default NULL,
  `product_name` varchar(50) default NULL,
  `product_model` varchar(200) default NULL,
  `product_made` varchar(200) default NULL,
  `in_quantity` decimal(18,2) default NULL,
  `in_price` decimal(18,2) default NULL,
  `unit` varchar(50) default NULL,
  `add_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `remark` varchar(200) default NULL,
  PRIMARY KEY  (`detail_id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_product`;

CREATE TABLE `$prefix_product` (
  `product_id` int(11) NOT NULL default '0',
  `product_name` varchar(50) default NULL,
  `product_model` varchar(200) default NULL,
  `product_made` varchar(200) default NULL,
  `product_tags` varchar(200) default NULL,
  `product_locations` longtext,
  `product_price` varchar(100) default NULL,
  `is_stock` int(11) default NULL,
  `product_sort` int(11) default NULL,
  `product_remark` varchar(250) default NULL,
  `is_include_component` INT(1),
  `is_not_used` INT(1),
  PRIMARY KEY  (`product_id`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_product_price`;

CREATE TABLE `$prefix_product_price` (
  `price_id` bigint(20) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `price_name` varchar(50) NOT NULL,
  `product_price` decimal(18,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `is_hide` int(11) default NULL,
  PRIMARY KEY  (`price_id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_product_tag`;

CREATE TABLE `$prefix_product_tag` (
  `product_id` int(11) NOT NULL default '0',
  `tag_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`product_id`,`tag_id`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_py`;

CREATE TABLE `$prefix_py` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `product_id` int(11) default NULL,
  `pym` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `index_pym` (`pym`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_sales`;

CREATE TABLE `$prefix_sales` (
  `batch_id` bigint(20) unsigned NOT NULL auto_increment COMMENT '单据号码',
  `sales_money` decimal(18,2) default NULL COMMENT '单据金额',
  `sales_day` varchar(20) default NULL,
  `sales_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '单据日期',
  `client_no` int(11) default NULL,
  `remark` varchar(200) default NULL,
  `summary` varchar(500) default NULL, 
  `sales_money_real` decimal(18,2) default NULL,
  PRIMARY KEY  (`batch_id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_sales_detail`;

CREATE TABLE `$prefix_sales_detail` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `batch_id` bigint(20) unsigned NOT NULL,
  `product_id` mediumint(9) default NULL,
  `product_name` varchar(100) default NULL,
  `product_model` varchar(200) default NULL,
  `product_made` varchar(200) default NULL,
  `sales_price` decimal(9,2) default NULL,
  `sales_ammount` decimal(9,2) default NULL,
  `sales_money` decimal(18,2) default NULL,
  `unit` varchar(50) default NULL,
  `remark` varchar(50) default NULL,
  `sales_money_real` decimal(9,2) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_stock`;

CREATE TABLE `$prefix_stock` (
  `product_id` bigint(20) NOT NULL default '0',
  `product_name` varchar(50) default NULL,
  `product_model` varchar(200) default NULL,
  `product_made` varchar(200) default NULL,
  `stock_quantity` decimal(18,2) default NULL,
  `stock_money` decimal(18,2) default NULL,
  `stock_price` decimal(18,2) default NULL,
  `stock_unit` varchar(50) default NULL,
  `last_upd_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `low_quantity` decimal(18,2) default 0,
  `remark` varchar(500) default NULL,
  PRIMARY KEY  (`product_id`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_tag`;

CREATE TABLE `$prefix_tag` (
  `tag_id` int(11) default NULL,
  `tag_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`tag_name`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_product_unit`;

CREATE TABLE `$prefix_product_unit`(
`product_id` INT,
`unit_name` VARCHAR(200),
`unit_quantity` DECIMAL(18,2),
`unit_sort` INT(2),
PRIMARY KEY(`product_id`,`unit_name`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_product_component`;

CREATE TABLE `$prefix_product_component`(
`master_product_id` INT,
`component_product_id` INT,
`component_product_quantity` DECIMAL(18,2),
PRIMARY KEY(`master_product_id`,`component_product_id`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_log`;

create table `$prefix_log`(
`log_id` bigint auto_increment,
`log_batch_id` bigint,
`page_name` varchar(100),
`action_name` varchar(100),
`sql_text` text,
`sql_type` varchar(50),
`execute_result` varchar(50),
`add_date` timestamp,
`add_user` varchar(100),
primary key(`log_id`)
) DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `$prefix_log_batch`;

create table `$prefix_log_batch`(
`log_batch_id` bigint auto_increment,
primary key(`log_batch_id`)
);

DROP TABLE IF EXISTS `$prefix_stock_detail`;

create table `$prefix_stock_detail`(
`id` bigint auto_increment,
`product_id` int,
`action_type` nvarchar(50),
`relate_table` varchar(50),
`relate_id` bigint,
`product_name`   varchar(100),
`product_model`   varchar(200),
`product_made`   varchar(200),
`quantity` decimal(18,2),
`unit` varchar(50),
`stock_before_quantity` decimal(18,2),
`stock_before_unit` varchar(50),
`stock_quantity` decimal(18,2),
`stock_unit` varchar(50),
`action_time` datetime,
`add_time` datetime,
primary key(`id`)
);

DROP TABLE IF EXISTS `$prefix_drug`;

create table $prefix_drug(
d_id bigint auto_increment,
d_name varchar(100),
d_model_made varchar(100),
d_remark varchar(200),
in_date date,
expire_date date,
del_flag int,
add_date timestamp,
primary key(d_id)
);

DROP TABLE IF EXISTS `$prefix_category`;

create table $prefix_category(
c_id bigint auto_increment,
c_id_parent bigint ,
c_sort int,
c_name varchar(100),
primary key(c_id)
);

DROP TABLE IF EXISTS `$prefix_product_category`;

create table $prefix_product_category(
product_id bigint,
category_id bigint,
primary key(product_id,category_id)
);

DROP TABLE IF EXISTS `$prefix_big_product_stock`;

CREATE TABLE `$prefix_big_product_stock` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `product_state` varchar(20) NOT NULL,
  `stock_position` varchar(50) DEFAULT NULL,
  `quantity` decimal(18,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `instock_batch_id` bigint(20) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `b_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `$prefix_customer_product`;

CREATE TABLE `$prefix_customer_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_no` int(11) DEFAULT NULL,
  `client_company` varchar(200) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `product_model` varchar(200) DEFAULT NULL,
  `product_made` varchar(200) DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `fake_price` varchar(50) DEFAULT NULL,
  `tax_price` varchar(50) DEFAULT NULL,
  `fake_tax_price` varchar(50) DEFAULT NULL,
  `price_unit` varchar(50) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `del_flag` int(11) DEFAULT NULL,
  `del_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `$prefix_art`;

CREATE TABLE `$prefix_art` (
  `art_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(255) NOT NULL,
  `art_title` varchar(255) DEFAULT NULL,
  `art_content` longtext,
  `add_date` datetime DEFAULT NULL,
  `sort_order` bigint(20) DEFAULT NULL,
  `summary` text,
  PRIMARY KEY (`art_id`)
);

DROP TABLE IF EXISTS `$prefix_art_cat`;

CREATE TABLE `$prefix_art_cat` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) DEFAULT NULL,
  `cat_sort` int(11) DEFAULT NULL,
  `cat_show_front` int(11) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
);

DROP TABLE IF EXISTS `$prefix_product_stock_group`;

create table $prefix_product_stock_group(
group_id int auto_increment,
group_name nvarchar(200),
primary key(group_id));

DROP TABLE IF EXISTS `$prefix_product_stock_group_detail`;

create table $prefix_product_stock_group_detail(
id int  auto_increment,
group_id int,
product_id int,
sort_order int,
primary key(id)
);

insert into 

