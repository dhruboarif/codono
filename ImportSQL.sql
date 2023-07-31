-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 01, 2020 at 05:34 AM
-- Server version: 5.7.19
-- PHP Version: 7.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ImportSQl`
--

-- --------------------------------------------------------

--
-- Table structure for table `codono_admin`
--

DROP TABLE IF EXISTS `codono_admin`;
CREATE TABLE IF NOT EXISTS `codono_admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(200) DEFAULT NULL,
  `username` char(16) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `cellphone` varchar(50) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `last_login_time` int(11) UNSIGNED DEFAULT NULL,
  `last_login_ip` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Administrators table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_admin`
--

INSERT INTO `codono_admin` (`id`, `email`, `username`, `nickname`, `cellphone`, `password`, `sort`, `addtime`, `last_login_time`, `last_login_ip`, `endtime`, `status`) VALUES
(1, 'support@your.exchange', 'admin', 'yourexchange', '13502182299', '21232f297a57a5a743894a0e4a801fc3', 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_adver`
--

DROP TABLE IF EXISTS `codono_adver`;
CREATE TABLE IF NOT EXISTS `codono_adver` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Ads pictures table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_adver`
--

INSERT INTO `codono_adver` (`id`, `name`, `url`, `img`, `type`, `sort`, `addtime`, `endtime`, `status`) VALUES
(8, 'Airdrops', 'Airdrop', '5d763e1f91ff0.png', NULL, 0, 1568050075, 1568050081, 1),
(5, 'Slider Title', '/Article/index/id/19.html', '5a314bc45a99c.jpg', '', 4, 1510675200, 1512921600, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_appads`
--

DROP TABLE IF EXISTS `codono_appads`;
CREATE TABLE IF NOT EXISTS `codono_appads` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `content` varchar(256) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `block_id` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Ads pictures table' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_appadsblock`
--

DROP TABLE IF EXISTS `codono_appadsblock`;
CREATE TABLE IF NOT EXISTS `codono_appadsblock` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `content` varchar(256) DEFAULT NULL,
  `rank` varchar(256) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `remain` varchar(255) DEFAULT '3',
  `type` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Ads pictures table' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_appc`
--

DROP TABLE IF EXISTS `codono_appc`;
CREATE TABLE IF NOT EXISTS `codono_appc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_name` varchar(64) DEFAULT NULL,
  `web_title` varchar(64) DEFAULT NULL,
  `web_icp` varchar(64) DEFAULT NULL,
  `index_img` varchar(256) DEFAULT NULL,
  `pay` varchar(256) DEFAULT NULL,
  `withdraw_notice` varchar(256) DEFAULT NULL,
  `charge_notice` varchar(256) DEFAULT NULL,
  `show_coin` varchar(255) DEFAULT NULL,
  `show_market` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_app_log`
--

DROP TABLE IF EXISTS `codono_app_log`;
CREATE TABLE IF NOT EXISTS `codono_app_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_app_vip`
--

DROP TABLE IF EXISTS `codono_app_vip`;
CREATE TABLE IF NOT EXISTS `codono_app_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `rule` text,
  `times` int(11) DEFAULT NULL,
  `price_num` varchar(255) DEFAULT NULL,
  `price_coin` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_app_vip`
--

INSERT INTO `codono_app_vip` (`id`, `tag`, `name`, `rule`, `times`, `price_num`, `price_coin`, `status`, `addtime`) VALUES
(1, '1', 'VIP Membership', '[{\"id\":\"1\",\"num\":1000}]', 100, '100', '3', 1, 1476004810);

-- --------------------------------------------------------

--
-- Table structure for table `codono_app_vipuser`
--

DROP TABLE IF EXISTS `codono_app_vipuser`;
CREATE TABLE IF NOT EXISTS `codono_app_vipuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `vip_id` int(11) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_article`
--

DROP TABLE IF EXISTS `codono_article`;
CREATE TABLE IF NOT EXISTS `codono_article` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `adminid` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `hits` int(11) UNSIGNED DEFAULT NULL,
  `footer` int(11) UNSIGNED DEFAULT NULL,
  `index` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `img` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `adminid` (`adminid`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `codono_article`
--

INSERT INTO `codono_article` (`id`, `title`, `content`, `adminid`, `type`, `hits`, `footer`, `index`, `sort`, `addtime`, `endtime`, `status`, `img`) VALUES
(50, 'Investors from 28 Countries Own Land in Norway’s “Private City” Liberstad', 'With slogans like “taxation is theft”, Liberstad is attracting more and more libertarians from around the world, local media reports. According to its website, 112 people have already bought land plots in the “anarcho-capitalist city” established on farmland, not far from Kristiansand in Southern Norway. The buyers come from 28 countries, including Norway, neighboring Sweden, distant Brazil and the United Kingdom. Another 500 potential investors have signed up on a waiting list.<br />\r\n<br />\r\nThe plots are sold for as little as 75,000 Norwegian Kronor, or $9,400 dollars for 1,000 m2, and as much as 375,000 NOK ($47,100 USD) for 5,000 m2. Payments in 27 different cryptocurrencies are currently accepted, including bitcoin cash (BCH) and bitcoin core (BTC). The team behind Liberstad plans to start handing over the purchased plots by 2020, when the first residents will be able to move in.<br />\r\n<br />\r\nLast summer, John Holmesland and Sondre Bjellås, bought the Tjelland farm in the municipality of Marnardal, where the city is located. Since then they have promoted the project and informed about its progress through social media and the Liberstad’s blog. In December, they announced that local authorities had granted concession and permission for ownership of the agricultural property where the city is being developed.<br />\r\n<br />\r\n<br />\r\n<br />\r\nPrivate Police and Other “Public” Services Planned<br />\r\nJohn Holmesland told the Norwegian outlet Local that Liberstad was inspired by Atlantic Station, a similar project within the city of Atlanta in the US state of Georgia. He and his partner want to eventually set up a private police force, a fire department, and a water utility for the city’s residents. Companies will be invited to provide these and other private (public) services.<br />\r\n<br />\r\nInvestors from 28 Countries Own Land in Norway’s “Private City” Liberstad“The only thing we demand is that you respect the principle of non-aggression and private property rights,” its founders state. According to local media, they may run into some issues in their attempt to achieve all that. Their plans have been dismissed by some Norwegian officials.<br />\r\n<br />\r\n“It may be that someone comes and settles there, but establishing a state within the state is not realistic,” Labor Party deputy Kari Henriksen told NRK, the Norwegian state-owned broadcaster. Henriksen, who is representing the local Vest-Agder constituency in the Norwegian parliament, believes that the residents of the “private city” will be dependent on the rest of the society in many ways.<br />\r\n<br />\r\nA similar project is the Free Republic of Liberland established on a disputed territory between Croatia and Serbia. It was proclaimed by the Czech libertarian Vít Jedlička in 2015. Another example worth mentioning is the Seasteading Institute’s plan to develop a floating city in the Pacific Ocean as a “permanent and politically autonomous settlement”.<br />\r\n<br />', 1, 'bbb', 0, 0, 1, 0, 1497669354, 1497628800, 1, ''),
(51, 'Bitcoin Was the Ninth Most Popular Wikipedia Article Last Year', 'Last year lots of people were inquiring about the cryptocurrency bitcoin and the word itself was one of the topmost trending words searched in 2017 according to Google Trends data. Another area where bitcoin was searched frequently was the website Wikipedia. The website hosts a free encyclopedia that is openly editable, while educational resources are also provided in 299 different languages. Wikipedia recently published its “Annual Top 50 Report” which includes a curated list of the top fifty most popular articles on Wikipedia throughout 2017.<br />\r\n<br />\r\nBitcoin Was the Ninth Most Popular Wikipedia Article Last Year<br />\r\nThe Wiki Bitcoin article was the ninth most popular last year on Wikipedia.<br />\r\nAccording to Wiki’s data, the ‘Bitcoin’ article was the ninth most popular encyclopedia post last year just below the ‘United States’ articles and just above the Netflix drama series ‘13 Reasons Why.’ Bitcoin stands among other top ten editorials documenting Donald Trump, Game of Thrones, and Queen Elizabeth II. The introduction in the Wiki Bitcoin article states:   <br />\r\n<br />\r\nBitcoin is a cryptocurrency and worldwide payment system. It is the first decentralized digital currency, as the system works without a central bank or single administrator. The network is peer-to-peer and transactions take place between users directly, without an intermediary.<br />\r\n<br />\r\nBitcoin Was the Ninth Most Popular Wikipedia Article Last Year<br />\r\nThe Wiki Bitcoin article is just below the ‘United States’ article, and above the Netflix original show ’13 Reasons Why.’<br />\r\nWiki Senior Editor: ‘Bitcoin the Much-Hyped “Future of Money”’<br />\r\nLast year the ‘Bitcoin’ article accumulated over 15 million views and the page peaked in traffic on December 8, 2017. In the annual report Wiki Senior Editor JFG gives the article a bit of an odd introduction.<br />\r\n<br />\r\n“For our dear readers who can’t make heads or tails of this novelty: Bitcoin is as good as gold, shinier than lead, bubblier than tulips, held deep in the mines, and driving people nuts,” explains the Wiki editor.  <br />\r\n<br />\r\nGold has enriched adventurers and bitcoin has held fools to ransom. You may dive in a pool of gold, but lose it all at war. Strangely, while you can still buy gold today and forget about it until your great-grandchildren cash it out, the much-hyped “future of money” has turned into the most speculative intangible asset of all time, while proving totally unsuitable as a means of payment.<br />\r\n<br />\r\nWithin the archives of 5,000 most popular articles from last week according to the Wiki page ‘User:west.andrew.g/popular pages,’ Bitcoin ranks at number 354. The page is aggregated from raw data which displays articles with at least 1,000 hits in a seven day period and only the most popular are published through the feed. Ethereum just makes the cut at 3710, Cryptocurrency 1273, and Blockchain slides ahead at 312. All of the data showing how popular digital currencies are on Wiki is derived from the company’s content consumption metrics which shows datasets of raw dump files and page views.<br />\r\n<br />\r\nWhat do you think about the Bitcoin article on Wikipedia placing 9th most popular in 2017? How do you think it will fare in 2018? Let us know in the comments below.<br />', 1, 'bbb', 0, 0, 1, 0, 1524897549, 1524897552, 1, ''),
(52, 'The Ukrainian Central Bank Is Expanding Its Blockchain Team', 'The National Bank of Ukraine is expanding the team working to move the country\'s national currency, the hyrvnia, to a blockchain.<br />\r\n<br />\r\nRevealed in an email to CoinDesk, the number of people added and who they are is being kept private for now, but the expansion shows a level of intent that hasn\'t been seen at many other central banks.<br />\r\n<br />\r\n\"Today, we\'ve reinforced our team with world-class professionals and are optimistic that the project will get a boost in upcoming months,\" wrote Yakiv Smolii, acting governor of the central bank.<br />\r\n<br />\r\nWhile the details of the team were not made public, CoinDesk last week reported that Ukraine-based Distributed Lab is helping with the build.<br />\r\n<br />\r\nDistributed Lab\'s founder, Pavel Kravchenko, confirmed that the startup is at least partly \"responsible for [the] architecture, blockchain research and development and security analysis\" of the institution\'s initiative.<br />\r\n<br />\r\nCentral banks across the globe have been discussing and exploring blockchain technology for its ability to more efficiently track funds and reduce the expenses of commercial banks. For instance, the People\'s Bank of China has deemed the creation of a fiat-based cryptocurrency a \"crucial\" financial development.<br />\r\n<br />\r\nBut still, the National Bank of Ukraine gave a more detailed vision of its undertaking to create a \"national digital currency,\" that makes its work less theoretical than others.<br />\r\n<br />\r\nAccording to Smolii:<br />\r\n<br />\r\n\"National bank of Ukraine is looking forward to implementation of e-hryvnia based on blockchain technology. We consider blockchain as the next step in evolution of transactions technologies, which will become more popular and widespread during the next decades.\"<br />\r\n<br />\r\n\'Convenient\' money<br />\r\nThe central bank formally began working on a blockchain-based system in November 2016 to develop a \"cashless economy.\"<br />\r\n<br />\r\nWhile the results of the earlier research were not disclosed, Smolli told CoinDesk, the project is now \"focused on studying the ability of the central bank to establish [an] operational e-hryvnia solution which would be available 24/7 and easy-to-use for all stakeholders.\"<br />\r\n<br />\r\nIn this way, the central bank is now working to build a more \"convenient instrument\" for Ukrainian citizens and businesses to conduct any number of transactions.<br />\r\n<br />\r\nDisplaying how determined the central bank is to move forward with the project, the National Bank of Ukraine has begun surveying commercial banks in the country, in an effort to understand their \"readiness\" to \"support the circulation\" of a national fiat currency riding on a blockchain.<br />\r\n<br />\r\nThe central bank is also looking at best practices employed by other blockchain users around the world.<br />', 1, 'bbb', 0, 0, 1, 0, 1525066282, 1525066285, 1, ''),
(53, 'Food industry companies block chain Alliance Block chain applications breakthrough again', '<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	2017year6month23Day, ushered in Jinan, China Food Industry Association event - the Third China Food Safety and the circulation of cold chain logistics Development Summit at Sheraton Jinan Hotel grand opening, the Ziyun stake Shuanghui logistics, missing food, Chinese food group, Chia Tai(China)Investment Co., Ltd., Zhengzhou one thousand flavor central kitchen and other 20 companies initiated the China Food Industry Alliance was formally established chain block, which is the industrys one small step, one giant leap for food safety, which marks the block chain applications reproduction industry breakthrough, Chinese food safety upgrade!\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	from2017year3month28May the State Administration of Food and Drug Administration issued Provisions on food production and operation of the establishment of this food traceability system, clearly provides food manufacturers, food enterprises, a variety of data catering enterprises established food traceability system must be traceable, especially food clearly defined transportation information will continue retrospective, at the same time explicitly require food traceability information must ensure that information need not modify technically, specification. The new policy of strict food from farm to table each pass, the Chinese food safety into the four most serious of the era.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	As bears the main responsibility for food safety of Chinese food industry enterprises, how to deal with tighter government regulation under the new policy requirements, the industry has been a sore point. China Food Industry Association fully aware of business needs, invited industry experts on food industry enterprises, food cold chain logistics enterprises in-depth interpretation of the meeting, corporate crack confused.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p><p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	As vice president of logistics and food Branch of China Food Industry Association, Luo Jianhui, chairman of Ziyun shares based on the new regulatory requirements of the State Food and Drug Administration launched the first cloud services platform based on food traceability chain block, without increasing the cost of doing business as much as possible premise, in the cloud model, combined with the shares of Ziyun car-free carrier services, using block chain technology for the food industry enterprises to provide a new generation of food safety traceability service, first in the industry to achieve food during storage and transportation information can be traced back , the companys various food safety traceability information in real-time write block chain, technically perfect solution to the problem of traceability data can not be modified, so that the food traceability system for food industry enterprises are able to meet the regulatory requirements of government regulatory authorities.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	Retrospective, for regulators and can not be traced. Luo Jianhui further stressed that the public service must be retroactive, so that consumers have a convenient way to trace food purchased, food traceability to make the transition from the original post-supervision as consumers daily consumption of prevention in advance, so you can avoid not meet the quality requirements of food to be consumed, so as to avoid more incidents of food safety, the protection of public food safety to the maximum extent.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	Ziyun shares based on the introduction of small micro-channel program block chain so that consumers can simply focus on the retrospective, is an indispensable assistant Consumer food safety and security.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	The new policy ground, we must consider the interests of business. Luo Jianhui put forward new ideas on how food production enterprises to implement the national food Administration new drug regulatory policies, A lot of the domestic food industry enterprises mention of retrospective thinking about to increase investment, to increase the cost, without a proper understanding of the food traceability system. to know that serious chaos in food production, a large number of substandard food in the market yield expelled through low-cost, construction traceability system is actually a highlight corporate product quality and food safety an important means of main responsibility is an important measure to better protect their own markets. \r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	In the practice of food traceability, Luo Jianhui team led by innovative Ziyun shares of closely integrated food traceability and marketing business to help companies achieve service delivery through the food product traceability system, so that consumers can simultaneously get to the traceability of products About eating or cooking of the food so that consumers better enjoy high quality food, increase consumer stickiness of their products, increase consumer re-purchase rate of the product. In addition Ziyun shares of food security treasure dating back service can help companies get a lot of personal consumption data, so that enterprises can be precision marketing, improve economic efficiency. In view of the participating companies, the shares of food traceability service Ziyun perfect combination of product and corporate marketing services together, to help enterprises solve food safety problems at the same time, but also solve the problem of large data precision marketing Internet era, such a platform only vitality, can the regulatory policy of the country landing.\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	After the State Food and drug Administration issued a new policy, food traceability service this market has become just need to market, market size reach 100 billion. Luo Jianhui said he was optimistic. The face of billions of market cake, Ziyun shares chose to share with the industry, Ziyun share scheme in the country each province to find a business scale in5000About million for third-party cold chain logistics companies to jointly develop food traceability service market, for consumers to strict farm to table every pass, so that consumers enjoy food safety!\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	<br />\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	Source: Netease News\r\n</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	</p>\r\n<p style=\"color:#333333;font-family:sans-serif;font-size:16px;background-color:#F9F9F9;\">\r\n	Disclaimer: This article is reprinted, has nothing to do with the sea through a network, only with the spread of news, does not mean<span style=\"color:#333333;font-family:sans-serif;font-size:16px;line-height:24px;background-color:#F9F9F9;\">Trading platform </span>Point of view, does not constitute<span style=\"color:#333333;font-family:sans-serif;font-size:16px;line-height:24px;background-color:#F9F9F9;\">Trading platform </span>Investment advice, fried currency risk, the investment need to be cautious! !\r\n</p>\r\n<br />\r\n<br />\r\n<div style=\"margin:0px;padding:0px;border:0px;font-family:sans-serif;color:#990000;font-size:13px;background-color:#F9F9F9;\">\r\n	Disclaimer: Some Article from the Internet, such as the reprint does not meet your wishes or infringe on your copyright, please contact us, we will remove as soon as possible.\r\n</div>', 1, 'bbb', 0, 1, 1, 0, 1498188169, 1498147200, 1, ''),
(54, '7 Facts You Might Not Know About Ripple', '<br />\r\nRipple was one of the top-performing cryptocurrencies of 2017, up by a staggering 35,500% for the year. And as a relatively new player in the cryptocurrency space, Ripple isn\'t well understood by as many people as, say, bitcoin and Ethereum are.<br />\r\n<br />\r\nWith that in mind, here are seven facts that will help familiarize you with Ripple, how the cryptocurrency works, and its key advantages over other leading digital tokens.<br />\r\n<br />\r\nRipples on surface of water.<br />\r\n1. Ripple isn\'t the cryptocurrency\'s official name<br />\r\nThe cryptocurrency commonly referred to as Ripple is officially called the XRP. In other words, there\'s technically no such thing as buying \"100 Ripple.\"<br />\r\n<br />\r\nRipple Labs is the name of the company that created the XRP token, and to be fair, Ripple is much easier to remember and a much catchier name, so that\'s why many people use the terms interchangeably.<br />\r\n<br />\r\nOn that topic, if you were ever wondering why bitcoin isn\'t generally capitalized, but cryptocurrencies like Ripple, Stellar, and others are, it\'s because bitcoin isn\'t the name of a company, while many other cryptocurrencies are.<br />\r\n<br />\r\n2. Ripple offers three different products, and only one uses the XRP token<br />\r\nOne interesting fact that even many Ripple owners are surprised to learn is that the company\'s flagship product, xCurrent, doesn\'t really use the XRP cryptocurrency at all.<br />\r\n<br />\r\nxCurrent is designed to allow banks to transact with one another, and to provide compatibility between any currencies, not just cryptocurrencies. In fact, this is the product used in the well-known partnership with American Express and Santander.<br />\r\n<br />\r\nThe product that trades XRP through the xCurrent system is known as xRapid. This supposedly has several key advantages, such as making transactions even faster and opening up new markets, but it isn\'t being widely used yet.<br />\r\n<br />\r\n<br />\r\n3. You can\'t mine Ripple<br />\r\nBitcoin\'s circulating supply gradually increases due to a process called mining, in which users pool their computing power to process transactions in exchange for newly minted \"blocks\" of tokens. Many other major cryptocurrencies also grow their supply through mining.<br />\r\n<br />\r\nHowever, Ripple is different. All 100 billion XRP that will ever be created already exist, although not all are in circulation yet, which we\'ll get to in the next section.<br />\r\n<br />\r\n4. Only about 40% of XPR tokens are in circulation<br />\r\nAlthough there are 100 billion XRP tokens in existence, the majority of them aren\'t in circulation yet. Ripple Labs owns about 60 billion XRP as of this writing, 6.25 billion of which are directly owned and 55 billion of which are in escrow accounts for future distribution. Over the next few years, 1 billion XRP will become available per month to be distributed. So, the circulating supply could increase dramatically in the coming years.<br />\r\n<br />\r\nBased on the current XPR price of just under $0.80, the value of all XRP in circulation is $31.52 billion, making it the third-largest cryptocurrency by market cap. However, the combined value of all XRP in existence is almost $80 billion.<br />', 1, 'bbb', 0, 0, 1, 0, 1498361419, 1498320000, 1, ''),
(55, 'The World Economic Forum published a detailed white paper, Understanding the potential of the block chain', 'YouTuber ‘Genius Trader’ has recently uploaded a video in which he claims Ripple could fly past $11.00 by the 25th of May, due to a potentially huge announcement.<br />\r\n<br />\r\nBefore I start, you can see the video for yourself, here- (Skip to 1:47) https://www.youtube.com/watch?v=nFoeSqh8ORU<br />\r\n<br />\r\nSo, what’s going on and how real is this?<br />\r\n<br />\r\nWell of course, it’s a Ripple video so the focus here is on Ripple potentially making a huge partnership. Rightly so, Genius Trader admits that in order for his predictions to ring true, Ripple needs to see a huge gain in market cap, a gain that is quite unlikely given the present gaps in the market. Regardless of this, the show must go on.<br />\r\n<br />\r\nTo cut to the chase, Genius Trader believes that Amazon will start accepting Ripple.<br />\r\n<br />\r\nRemember what happened to Verge when that rumour spread regarding XVG? Just imagine how huge an Amazon partnership would be for Ripple. Genius Trader now makes an interesting point that sort of reinforces his theory.<br />\r\n<br />\r\nBasically, in order to grow to $11.00, Ripple needs to really bolster its market capitalisation right? Well Amazon have a NET worth that could possibly contribute to that, after reports this week came out stating that overall, Amazons NET worth reached $105.1Billion, according to Genius Traders video.<br />\r\n<br />\r\nMoreover, Ripple can offer something incredible in return to Amazon, lost cost, high speed transactions that would allow Amazon to improve the efficiency at which it facilitates cross border payments.<br />\r\n<br />\r\nRight okay, so all of the above is sort of fair enough, it’s heavily speculative but I guess he has a point, the question I am asking now however- where does the 25th of May fit into this, is this just a complete stab in the dark?<br />\r\n<br />\r\nHow realistic is this?<br />\r\n<br />\r\nIt’s probably not going to happen soon and really, I doubt we will see Amazon accepting XRP as a payment option  before the 25th of May. I just think Amazon are way to big for this. If Amazon wanted to dip into cryptocurrency, I’m pretty sure they would have done it by now. They have the facilities and the finances to enter the blockchain themselves and obviously, an Amazon native currency would be most beneficial to them.<br />', 1, 'bbb', 0, 1, 1, 0, 1524897686, 1524897689, 1, ''),
(56, 'FLIP – token for gamers from gaming experts', 'Dear traders,<br />\r\n<br />\r\nWe are happy to present a new token joining our exchange – the FLP token from Gameflip.<br />\r\n<br />\r\nThis year alone, there are nearly 2 billion gamers and a growing USD $94.4 billion in revenue from ‘direct-from-publisher’ in-game digital items. Experts predict that it would only grow by 2020.<br />\r\n<br />\r\n<br />\r\nGamers spend a significant amount of their lives earning and accumulating digital items within the game worlds they are a part of. However, if the player leaves a game, those in-game items, bought and earned, go to waste. Gameflip has developed a solution aiming to help gamers liquidate those goods.<br />\r\n<br />\r\n <br />\r\n<br />\r\nGameflip has extensive experience operating a digital marketplace for the buying and selling of in-game digital items and has already accumulated a notable audience of over 2M gamers. Now the company is focusing its efforts on providing gamers liquidity via a secure, transparent ecosystem based on blockchain.<br />\r\n<br />\r\n <br />\r\n<br />\r\nThe Gameflip team is made up of gaming industry veterans and has an advantage of having strong, established relationships with the gatekeepers of the industry – the game publishers that own and control the games and therefore, digital goods generated within. These ties developed over the past decade may significantly contribute to spreading the ecosystem and the FLP token.<br />', 1, 'aaa', 0, 0, 1, 0, 1525073427, 1525073429, 1, ''),
(40, 'What Bitcoin Is Really Worth May No Longer Be Such A Mystery', '<p>\r\n	It took two economists one three-course meal and two bottles of wine to calculate the fair value of one Bitcoin: $200.<br />\r\n<br />\r\nIt took an extra day for them to realize they were one decimal place out: $20, they decided, was the right price for a virtual currency that was worth $1,200 a year ago, flirted with $20,000 in December, and is still around $8,000. Setting aside the fortunes lost on it this year, Bitcoin, by their calculation, is still overvalued, to the tune of about 40,000 percent. The pair named this the Côtes du Rhône theory, after the wine they were drinking.<br />\r\n<br />\r\n“It’s how we get our best ideas. It’s the lubricant,” says Savvas Savouri, a partner at a London hedge fund who shared drinking and thinking duties that night with Richard Jackman, professor emeritus at the London School of Economics. Their quest is one shared by the legions of traders, techies, online scribblers, and gamblers and grifters mesmerized by Bitcoin. What’s the value of a cryptocurrency made of code with no country enforcing it, no central bank controlling it, and few places to spend it? Is it $2, $20,000, or $2 million? Can one try to grasp at rational analysis, or is this just the madness of crowds?<br />\r\n<br />\r\nAnswering this question isn’t easy: Buying Bitcoin won’t net you any cash flows, or any ownership of the blockchain technology underpinning it, or really anything much at all beyond the ability to spend or save it. Maybe that’s why Warren Buffett once said the idea that Bitcoin had “huge intrinsic value” was a “joke”—there’s no earnings potential that can be used to estimate its value. But with $2 billion pumped into cryptocurrency hedge funds last year, there’s a lot of money betting the punchline is something other than zero. If Bitcoin is a currency, and currencies have value, surely some kind of stab—even in the dark—should be made at gauging its worth.<br />\r\n<br />\r\nWriting on a tablecloth, Jackman and Savouri turned to the quantity theory of money. Formalized by Irving Fisher in 1911, with origins that go back to Copernicus’s work on the effects of debasing coinage, the theory holds that the price of money is linked to its supply and how often it’s used.<br />\r\n<br />\r\nHere’s how it works. By knowing a money’s total supply, its velocity—the rate at which people use each coin—and the amount of goods and services on which it’s spent, you should be able to calculate price. Estimating Bitcoin’s supply at about 15 million coins (it’s currently a bit more), and assuming each one is used an average of about four times a year, led Jackman and Savouri to calculate that 60 million Bitcoin payments were supporting their assumed $1.2 billion worth of total U.S. dollar-denominated purchases. Using the theory popularized by Fisher and his followers, you can—simplifying things somewhat—divide the $1.2 billion by the 60 million Bitcoin payments to get the price of Bitcoin in dollars. That’s $20.\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 1, 'aaa', 0, 1, 1, 0, 1524897880, 1524897885, 1, '59426a65d0dac.png'),
(41, 'Bigger Blocks and Smarter Contracts: What\'s In Bitcoin Cash\'s Next Fork?', 'Bitcoin cash\'s next software upgrade may be even more ambitious than its first - and that\'s no small feat given last time it broke off from bitcoin in acrimonious fashion.<br />\r\n<br />\r\nIn fact, the update, announced in November and slated for May 15, packages together a number of features that all seem about helping the network process more transactions than the original bitcoin (while adding more variety to features). Perhaps most notably, the change will quadruple bitcoin cash\'s block size parameter from 8 MB to 32 MB, allowing for vastly more transactions per block.<br />\r\n<br />\r\nBut while that might sound aggressive given bitcoin\'s more limited approach, those who have been following the cryptocurrency might be surprised that such an aggressive shift wasn\'t pursued sooner.<br />\r\n<br />\r\nAfter all, last fall, bitcoin cash\'s developers chose to ignore the protests of bitcoin\'s more seasoned developers, who had long argued that increasing the block size and moving the cryptocurrency forward too fast could jeopardize the more than $157 billion network.<br />\r\n<br />\r\nBut that contrarian mentality has proved, at least partially, attractive - one bitcoin cash is going for a little less than $1,500 a coin, making it\'s market cap more than $24 billion.<br />\r\n<br />\r\nIndeed, Joshua Yabut, who contributes to the bitcoin cash protocol\'s main software implementation, BitcoinABC, said he doesn\'t expect any protest at all when users are finally given the choice to upgrade software.<br />', 1, 'aaa', 0, 1, 1, 0, 1497525005, 1497456000, 1, '59426b2338444.png'),
(42, 'Crypto Market In Green Following Correction, Bitcoin Above $9,000, EOS Gains Significantly', 'Friday, April 27: after a mid-week correction that has seen Bitcoin go below $9,000, the crypto market is back on track with all top 10 cryptocurrencies listed on Coin360 in the green.<br />\r\n<br />\r\nCOIN360<br />\r\n<br />\r\nBitcoin (BTC) is above $9,000 again, trading at about $9,263 with a value gain of about 5 percent over 24 hours to press time.<br />\r\n<br />\r\nBTC Vatue &amp; Volume<br />\r\n<br />\r\nEthereum (ETH) is steadily climbing towards the $700 mark, trading at around $678 at press time, up almost 8 percent from yesterday.<br />\r\n<br />\r\nETH Value &amp; Volume<br />\r\n<br />\r\nTotal market cap is again over $400 bln, currently at $417 bln, after having dropped to as low as $380 bln Thursday.<br />\r\n<br />\r\nTotal Market Capitalization<br />\r\n<br />\r\nEOS continues to grow significantly, seeing 19 percent gains over 24 hours and trading around $17.40 at press time. With a growth of almost 200 percent over the last 30 days, the cryptocurrency’s price is approaching the levels of January, according to Coinmarketcap data.<br />\r\n<br />\r\nEOS Charts<br />\r\n<br />\r\nStellar (XLM) has increased by almost 10 percent over 24 hours - the altcoin is currently trading at $0.41.<br />\r\n<br />\r\nExcepting this week’s temporary decline, the crypto market has been moving upwards since the day Bitcoin’s price jumped $1,000 in 30 minutes on April 12. During this period, the markets have been propelled by a number of positive news, particularly Goldman Sachs executives resigning for positions at crypto projects, such as the crypto wallet Blockchain.com and the crypto merchant bank Galaxy Digital.<br />\r\n<br />\r\nEarlier today, Gil Beyda, the managing director of the venture capital arm of Comcast, expressed a bullish view on Bitcoin and real world applications of Blockchain technology.<br />\r\n<br />\r\nOn Monday, Goldman Sachs hired cryptocurrency trader Justing Schmidt as vice president of digital asset markets of its securities division, “in response to client interest in various digital products.”<br />\r\n<div>\r\n	<br />\r\n</div>', 1, 'aaa', 0, 0, 1, 3, 1524898085, 1524898092, 1, '59426b5715ef3.png'),
(43, 'Is the Tokyo bitcoin whale set to strike again?', '<p>\r\n	Speculation is growing that the trustees for the now-defunct Mt. Gox cryptocurrency exchange are getting set for another significant bitcoin sale.<br />\r\n<br />\r\nOn April 26, a website that tracks the Mt. Gox cryptocurrency wallets indicated that the trust had shifted a sizable portion of bitcoins and Bitcoin Cash from its wallet, which led many to speculate that Nobuaki Kobayashi, the head attorney for the Mt. Gox trust who’s been dubbed the “Tokyo whale,” is set to unload another lot of the recovered coins.<br />\r\n<br />\r\n“Some in the [cryptocurrency] community have been a bit worried about the recent movement of bitcoin out of the Mt. Gox settlement wallets,” wrote Mati Greenspan, senior market analyst at etoro.<br />\r\n<br />\r\n“Indeed, it appears that Kobayashi has moved about 16,000 BTC out of cold storage, which may indicate that he’s preparing to sell them on the open market,” Greenspan said.<br />\r\n<br />\r\n<br />\r\nMt. Gox trust cryptocurrency balance<br />\r\nIf the trustees were to proceed with a sale, it would be the first sizable transaction since the trust dumped nearly $400 million worth of bitcoin and Bitcoin Cash in March.<br />\r\n<br />\r\nAt current market value, the 16,000 bitcoins BTCUSD, +3.94%&nbsp; and 16,000 Bitcoin Cash are worth a combined $170 million and should the sale happen, onlookers will be scouting for potential price fluctuations.<br />\r\n<br />\r\n“If they are smart about it, it won’t have a material impact,” said Martin Garcia, managing director and co-head of sales and trading, at Genesis. “However, it all depends on the market at the time. If it’s going up the market will absorb it easily.”<br />\r\n<br />\r\nGarcia added that if done over the counter, it would be possible for the transaction to be executed in a single block.<br />\r\n	<div>\r\n		<br />\r\n	</div>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 1, 'aaa', 0, 0, 1, 4, 1524898182, 1524898189, 1, '59426c26a6e49.png'),
(44, 'Banking Giant ING Is Quietly Becoming a Serious Blockchain Innovator', '<p>\r\n	ING is out to prove that startups aren\'t the only ones that can advance blockchain cryptography.<br />\r\n<br />\r\nRather than waiting on the sidelines for innovation to arrive, the Netherlands-based bank is diving headlong into a problem that it turns out worries financial institutions as much as average cryptocurrency users. In fact, the bank first made a splash in November of last year by modifying an area of cryptography known as zero-knowledge proofs.<br />\r\n<br />\r\nSimply put, the code allows someone to prove that they have knowledge of a secret without revealing the secret itself.<br />\r\n<br />\r\nOn their own, zero-knowledge proofs were a promising tool for financial institutions that were intrigued by the benefits of shared ledgers but wary of revealing too much data to their competitors. The technique, previously applied in the cryptocurrency world by zcash, offered banks a way to transfer assets on these networks without tipping their hands or compromising client confidentiality.<br />\r\n<br />\r\nBut ING has came up with a modified version called \"zero-knowledge range proofs,\" which can prove that a number is within a certain range without revealing exactly what that number is. This was an improvement in part because it uses less computational power and therefore runs faster on a blockchain.<br />\r\n<br />\r\nFor example, zero-knowledge range proofs (which the bank open-sourced last year) can be used to prove that someone has a salary within the range needed to attain a mortgage without revealing the actual figure, said Mariana Gomez de la Villa, global head of ING\'s blockchain program.<br />\r\n<br />\r\n\"It can be used to protect the denomination of a transaction, but still allowing validation that there\'s enough money in the participant account to settle the transaction,\" she said.<br />\r\n<br />\r\nNow, building on its past work, ING is adding yet another wrinkle to enterprise blockchain privacy, leveraging a type of proof known as \"zero-knowledge set membership.\"<br />\r\n<br />\r\nRevealed exclusively to CoinDesk, ING plans to take the zero-knowledge concept beyond numbers to include other types of data.&nbsp;&nbsp;<br />\r\n<br />\r\nSet membership allows the prover to demonstrate that a secret belongs to a generic set, which can be composed of any kind of information, like names, addresses and locations.<br />\r\n<br />\r\nThe potential applications of set membership are wide-ranging, Gomez de la Villa said. Not restricted to numbers belonging to an interval, it can be used to validate that any sort of data is correctly formed.<br />\r\n<br />\r\n\"Set membership is more powerful than range proofs,\" Gomez de la Villa told CoinDesk, adding:<br />\r\n<br />\r\n\"For example, imagine that you could validate that someone lives in a country that belongs to the European Union, without revealing which one.\"<br />\r\n<br />\r\nBenefits of openness<br />\r\nBut you don\'t have to just take ING\'s word for it. Since being open-sourced, the body of cryptographic work that ING is building on has been subjected to academic to peer review at the highest levels.<br />\r\n<br />\r\nMIT math whiz and one of the co-founders of zcash, Madars Virza, revealed a vulnerability in last year\'s zero-knowledge range proofs paper. Virza showed that, in theory, it was possible to reduce the range interval and so glean knowledge about a hidden number.<br />\r\n<br />\r\nING said it has since fixed this vulnerability, and Gomez de la Villa pointed out that this is the type of contribution expected from the ecosystem where the very purpose of open-sourcing is allowing users to fix bugs and improve functions.<br />\r\n<br />\r\n\"By making the source code available, improving our zero-knowledge range proof solution has become a collaborative effort,\" she said.&nbsp;&nbsp;<br />\r\n<br />\r\nShe also framed the incident as an example of a mutually beneficial relationship between academic cryptographers and enterprises like ING.<br />\r\n<br />\r\n\"They are working on the theory; we are working on the practice,\" Gomez de la Villa said, adding:<br />\r\n<br />\r\n\"They can keep thinking about their crazy stuff and then we can say, \'OK, how can we use it in order to make it available to the rest so it can actually work?\'\"<br />\r\n<br />\r\nJack Gavigan, chief operating officer at Zerocoin Electric Coin Company, the company that develops the zcash network, said this type of open-source collaboration is contributing to a body of knowledge that all can draw upon, thus driving progress in the zero-knolwedge proof space at a rapid click. And those benefits will be returned in full.<br />\r\n<br />\r\n\"When a disruptive technology like blockchain comes along, it can shake things up, and companies that are best-positioned to embrace and exploit that technology are likely to end up at the top of the pile when things have settled down,\" said Gavigan.<br />\r\n<br />\r\nHe continued:<br />\r\n<br />\r\n\"I think that\'s why you see companies like ING delving into this space, getting hands-on with the technology, and joining the broader community - because when this technology matures and is ready for prime time, they\'ll be ready and able to hit the ground running.\"<br />\r\n<br />\r\nPicking up from JPM<br />\r\nIn other ways, the blockchain-savvy move is already paying off.<br />\r\n<br />\r\nING has been invited to the table with the world\'s top cryptographers and will participate in an invite-only workshop in Boston seeking to standardize zero knowledge proofs, alongside the likes of MIT\'s Shafi Goldwasser.&nbsp;&nbsp;<br />\r\n<br />\r\nIn this way, ING is now part of a wide community of experts extending the scope of zero-knowledge proofs.<br />\r\n<br />\r\nAt the start of this year, University College of London\'s Jonathan Bootle and Stanford\'s Benedikt Bunz released \"Bulletproofs,\" which dramatically improves proof performance and allows proving a much wider class of statements than just range proofs. Many startups have jumped on this and it\'s being taken into the enterprise space by the likes of Silicon Valley startup Chain.<br />\r\n<br />\r\nAmong banks, though, the best known implementation of zero-knowledge proofs is in JPMorgan Chase\'s Quorum, which was showcased to a rapturous reception on the blockchain circuit last year.<br />\r\n<br />\r\nTaking the Quorum model a step further, ING designed its range proofs to be computationally less onerous than previous zero knowledge deployments and so faster to run on distributed ledgers.<br />\r\n<br />\r\n\"Zk-SNARKs, used in JPM Quorum, are known to be less efficient than the construction of zero knowledge proofs for a specific purpose, as is the case of zero-knowledge range proofs. Indeed, range proofs are at least an order of magnitude faster,\" said Gomez de la Villa.<br />\r\n<br />\r\nAt JPMorgan, the Quorum team was led by Amber Baldet, who has since left to join a yet-to-be named startup. Now the word on the street is that JPMorgan is considering spinning out Quorum so it\'s not longer under the direct purview of the Wall Street giant, in a possible bid to gain more of a network effect from other banks.<br />\r\n</p>', 1, 'aaa', 0, 0, 1, 2, 1525065769, 1525065771, 1, '59426e46ae85b.png'),
(46, 'Trading platform  (www.your.exchange) Formally launched', '<p class=\"MsoNormal\">\r\n	<span></span>\r\n	<div style=\"margin:0px 14.3906px 0px 28.7969px;padding:0px;font-family:&quot;font-size:14px;background-color:#FFFFFF;\">\r\n		<h2 style=\"font-weight:400;font-family:DauphinPlain;font-size:24px;\">\r\n			What is Lorem Ipsum?\r\n		</h2>\r\n		<p style=\"text-align:justify;\">\r\n			<strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n		</p>\r\n	</div>\r\n	<div style=\"margin:0px 28.7969px 0px 14.3906px;padding:0px;font-family:&quot;font-size:14px;background-color:#FFFFFF;\">\r\n		<h2 style=\"font-weight:400;font-family:DauphinPlain;font-size:24px;\">\r\n			Why do we use it?\r\n		</h2>\r\n		<p style=\"text-align:justify;\">\r\n			It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n		</p>\r\n	</div>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', 1, 'aaa', 0, 0, 1, 5, 1525066038, 1525066040, 1, '5ae6cc0845178.png');

-- --------------------------------------------------------

--
-- Table structure for table `codono_article_type`
--

DROP TABLE IF EXISTS `codono_article_type`;
CREATE TABLE IF NOT EXISTS `codono_article_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `remark` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `index` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `footer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shang` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `codono_article_type`
--

INSERT INTO `codono_article_type` (`id`, `name`, `title`, `remark`, `index`, `footer`, `shang`, `content`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, 'Company Profile', 'Company Profile', 'Industry News', '0', '1', 'aboutus', '<p class=\"MsoNormal\">\r\n	<span></span>Trading platform <span>(</span><span>www.your.exchange</span><span>) Formally launched</span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	Trading platform digitalgoodsCurrency trading platform professional encrypted digital currency online trading platform.\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<span>We hope that through the integration of resources accumulated over the years of globalization and digital currency in line with the trend of globalization, technology-based, to help build a global financial center Internet as the ultimate goal, so that the platform a</span><span>International</span><span>Digital assets and digital currency trading platform Industry Standard organization</span><span>.</span> \r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<b><span>Platform advantages:</span></b><b></b> \r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n1,The most cutting-edge technology block chain system. We have a complete transaction system and digital encryption,<span></span>Trading platform <span>Trading platform block chain does not rely on third-party system, for storing network data distributed through its own node, verification and transmission technology, having a block chain to the central storage technology, information is highly transparent, non-tampering security features, and can achieve online and offline financial transactions docking full coverage, block chain technology will subvert the entire Internet infrastructure, and thus have a profound impact on the industry, known as the block chain</span>Fourth industrial revolution.\r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n2,<span></span>Trading platform <span>Trading platform has developed into an encrypted digital currency as the core business of a diversified investment platform, comprehensive digital asset trading platform, serving the worlds leading brand of encrypted digital currency investment transactions.</span> \r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n3,<span></span>Trading platform <span>Bitcoin trading platform support,</span><span>Ethernet Square</span><span>And other transaction encrypted digital currency.</span><span></span>Trading platform <span>Trading platform</span><span>Even block applications</span>As the core, and build membership system<span></span>Trading platform <span>Trading Platform wallet</span>Brick and mortar businesses and integrate the flow of the whole industry chain finance investment mode.<span></span>Trading platform Supports two-waytransaction,<span>low</span><span>Fees,Global arbitraryAccountReal-time arrival.</span><span></span>Trading platform <span>Trading Platform</span>With a strong block chain system provides transparent transaction, safe, reliable, efficient service revenue doubled.\r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n<span></span>Trading platform <span>It is encryptedDigital CurrencyConsumer businesses, online payment gradually integrate circulation, it is changing the storage, use and payment of funds to build a more secure and efficient encryptionDigital CurrencyThe internet,future,</span><span></span>Trading platform <span>We will provide more high-value digital assets services to global investors.</span> \r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<span><br />\r\n</span> \r\n</p>\r\n<p class=\"p\" style=\"text-indent:0pt;background:#FFFFFF;\">\r\n	<br />\r\n</p>', 1, 1521717888, 1521717890, 1),
(3, 'help', 'Help', 'Help', '0', '1', '', '', 2, 0, 0, 1),
(4, 'aboutus', 'About', 'about us', '0', '1', '', '', 1, 1498792179, 1498792179, 1),
(5, 'Contact us', 'Contact us', '', '0', '1', 'aboutus', '<p>\r\n	<span><span style=\"font-size:14px;\"><strong>Contact Us&nbsp;</strong></span></span>\r\n</p>\r\n<p>\r\n	<span><span style=\"font-size:14px;\"><br />\r\n</span></span>\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<span><span style=\"font-size:14px;\">Info here</span></span> \r\n</p>', 2, 1497496071, 1497496074, 1),
(6, 'join us', 'Join us', '', '0', '1', 'aboutus', '<span><span style=\"font-size:14px;line-height:21px;\">\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		Job Description\r\n	</h6>\r\n	<p>\r\n		Named among Fortune’s 2018 World’s Most Admired Companies, Flex offers a world of innovation, learning opportunities, and a strong reputation as environmentally responsible citizens. We are a leading sketch-to-scale™ company that designs and builds intelligent products for a connected world. With more than 200,000 professionals across 30 countries, and a promise to help the world Live smarter™, Flex provides innovative design, engineering, manufacturing, real-time supply chain insight and logistics services to companies of all sizes in various industries and end-markets.\r\n	</p>\r\n	<p>\r\n		With more than 100,000 team members globally, we promote an environment that is rooted in the entrepreneurial spirit in which the company was founded. Dell ’ s team members are committed to serving our communities, regularly volunteering for over 1,500 non-profit organizations. The company has also received many accolades from employer of choice to energy conservation. Our team members follow an open approach to technology innovation and believe that technology is essential for human success.\r\n	</p>\r\n	<p>\r\n		We are looking for a&nbsp;<span style=\"font-weight:700;\">Interaction UX/UI Industrial Designer</span>&nbsp;for our&nbsp;<span style=\"font-weight:700;\">Product Development</span>&nbsp;team!\r\n	</p>\r\n</div>\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		Responsibilities:\r\n	</h6>\r\n	<p class=\"content-group-sm\">\r\n		You will work closely with our product owners and the analytics team to help drive and ensure a best-in-class user experience on web, tablet and mobile platforms. With your knowledge and passion for keeping up-to-date with the latest advances in user interface design and web related technologies. You will be creating high quality designs with the goal of ensuring continual improvement of our sites. To realise this you will guide and set the standards and design principles for all of our brands to follow and work towards to enhance their online success.\r\n	</p>\r\n	<ul class=\"list\">\r\n		<li>\r\n			Gather, analyze, record and report on current market information with regard to the latest transportation methods.\r\n		</li>\r\n		<li>\r\n			Work with the team to determine company and customer needs and make recommendations on cost effective transportation methods and assist in price negotiations if appropriate.\r\n		</li>\r\n		<li>\r\n			Ensures lowest cost transportation by analyzing company and customer needs, researching transportation methods and auditing carrier costs and performances.\r\n		</li>\r\n		<li>\r\n			Ensure laws, rules and regulations regarding shipping/transportation methods are adhered to and prepares applications for appropriate certifications and licenses.\r\n		</li>\r\n		<li>\r\n			Prepare application for import / export control certifications and licenses (control documents).\r\n		</li>\r\n		<li>\r\n			Maintain logs and compile information on routes, rates and services on various vendors.\r\n		</li>\r\n		<li>\r\n			Arranges shipping details such as packing, shipping, and routing of product.\r\n		</li>\r\n		<li>\r\n			Analyzes and recommends transportation and freight costs as well as appropriate routing and carriers to be used.\r\n		</li>\r\n		<li>\r\n			Plans, schedules, and routes inbound and outbound domestic and international shipments of freight, using knowledge of applicable laws, tariffs, and Flextronics policies.\r\n		</li>\r\n		<li>\r\n			Be familiar with compliance required for corporate, and facility policies and procedures and assist the team in ensuring the highest standards are adhered to in the process.\r\n		</li>\r\n		<li>\r\n			Ensure Traffic Metrics are maintained and updated on a daily/weekly/monthly basis.\r\n		</li>\r\n		<li>\r\n			Establish and maintain good relationships with agents / suppliers in order to achieve quality of service and consistent cost reduction.\r\n		</li>\r\n		<li>\r\n			May schedule company vehicles for service and normal maintenance checks and is responsible for ensuring that all are registered and have the proper insurance.\r\n		</li>\r\n		<li>\r\n			Support the team in terms of knowledge and experience in dealing with daily operational and transportation issues.\r\n		</li>\r\n	</ul>\r\n</div>\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		Requirements:\r\n	</h6>\r\n	<ul class=\"list\">\r\n		<li>\r\n			Undergraduate Industrial Design/Graphic Design degree and 6-8 years relevant experience or Graduate degree in a related field, plus 4-6 years relevant experience\r\n		</li>\r\n		<li>\r\n			Strong skillset in digital design with an emphasis on Windows, mobile (iOS/Android), and web User Interfaces\r\n		</li>\r\n		<li>\r\n			Ability to distill complex problems into intuitive, clean, user friendly designs\r\n		</li>\r\n		<li>\r\n			Expert in User Experience concepts, Information Architecture, and software brand strategy\r\n		</li>\r\n		<li>\r\n			Experience with creating detailed workflow specifications and behaviors for development teams\r\n		</li>\r\n		<li>\r\n			Can process and integrate research studies, reports, trends, data, and information into plans, deliverables, and recommendations\r\n		</li>\r\n	</ul>\r\n</div>\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		Desired Skills and Experience:\r\n	</h6>\r\n	<ul class=\"list\">\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Strategic Thinking.</span>You will not only solve design issues but will proactively offer ideas and insights to improve the customer\'s experience and visual challenges.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Creative Suite.</span>Primarily Photoshop and Illustrator with some InDesign. Experience with Adobe Muse is also helpful.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Typography.</span>We need a designer who knows typography visual hierarchy and styles and how to use them properly. Your work will be for the whole EMEA region and be translated into dozens of languages. Any experience with Asian and Middle Eastern languages and fonts will be useful.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Experimentation.</span>Experience optimizing designs based on A/B testing is a plus.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Communication.</span>We need someone who can own their time but also knows how to ask the right questions to ensure the right message is communicated in the right way.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Marketing.</span>You are familiar with and have previously worked on a marketing team.\r\n		</li>\r\n		<li>\r\n			<span class=\"display-block\" style=\"font-weight:700;\">Asset Management.</span>Assist with development and maintenance of a digital asset management system.\r\n		</li>\r\n	</ul>\r\n</div>\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		What we offer:\r\n	</h6>\r\n	<ul class=\"list\">\r\n		<li>\r\n			A learning prone environment where employee development and satisfaction lies at the heart of the organisation\r\n		</li>\r\n		<li>\r\n			You choose and change your workplace besides our open office in our café area, or your home\r\n		</li>\r\n		<li>\r\n			Life at Dell means collaborating with dedicated professionals with a passion for technology\r\n		</li>\r\n		<li>\r\n			When we see something that could be improved, we get to work inventing the solution\r\n		</li>\r\n		<li>\r\n			Our people demonstrate our winning culture through positive and meaningful relationship\r\n		</li>\r\n		<li>\r\n			We invest in our people and offer a series of programs that enables them to pursue a career that fulfills their potential\r\n		</li>\r\n		<li>\r\n			Our team members ’ health and wellness is our priority as well as rewarding them for their hard work\r\n		</li>\r\n	</ul>\r\n</div>\r\n<div class=\"content-group-lg\" style=\"color:#333333;font-family:Roboto, &quot;font-size:13px;background-color:#FFFFFF;\">\r\n	<h6 class=\"text-semibold\" style=\"font-family:inherit;font-weight:500;color:inherit;font-size:15px;\">\r\n		Interested?\r\n	</h6>\r\n	<p>\r\n		We look forward to hearing from you! Please apply directly using the apply button below or via our website. In case you have any further questions about the role, you are welcome to contact Scott Ot, Recruitment Specialist on phone +01234567890.\r\n	</p>\r\n</div>\r\n</span></span>', 4, 1523761584, 1523761590, 1),
(7, 'Legal Notices', 'Legal Notices', '', '0', '1', 'aboutus', '<div align=\"center\" class=\"MsoNormal\" style=\"text-align:left;\">\r\n	<strong><span style=\"line-height:150%;font-size:26px;\">DISCLAIMER</span></strong> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<br />\r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<br />\r\n</div>\r\n<div class=\"MsoNormal\">\r\n	<strong><span style=\"line-height:115%;font-size:19px;\">WEBSITE DISCLAIMER</span></strong> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<span style=\"color:#595959;\">&nbsp;</span> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<span style=\"color:#595959;font-size:15px;\">The information provided by your.exchange(“we,” “us” or “our”) on&nbsp;<span>https://your.exchange&nbsp;</span>(the “Site”) and our mobile application&nbsp;is for general informational purposes only. All information on the Siteand our mobile application&nbsp;is provided in good faith, however we make no\r\nrepresentation or warranty of any kind, express or implied, regarding the\r\naccuracy, adequacy, validity, reliability, availability or completeness of any\r\ninformation on the Site&nbsp;or our mobile application. UNDER NO CIRCUMSTANCE SHALL WE HAVE ANY LIABILITY TO YOU FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF THE USE OF THE SITEOR OUR MOBILE APPLICATION&nbsp;OR RELIANCE ON ANY\r\nINFORMATION PROVIDED ON THE SITE&nbsp;AND OUR MOBILE APPLICATION. YOUR USE OF THE SITEAND OUR MOBILE APPLICATION&nbsp;AND YOUR RELIANCE ON ANY INFORMATION ON THE SITEAND OUR MOBILE APPLICATION&nbsp;IS SOLELY AT YOUR OWN RISK.</span> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<span style=\"color:#595959;\">&nbsp;</span> \r\n</div>\r\n<div class=\"MsoNormal\">\r\n	<br />\r\n</div>\r\n<div class=\"MsoNormal\">\r\n	<strong><span style=\"line-height:115%;font-size:19px;\">EXTERNAL LINKS\r\nDISCLAIMER</span></strong> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<span style=\"color:#595959;\">&nbsp;</span> \r\n</div>\r\n<div class=\"MsoNormal\" style=\"text-align:justify;\">\r\n	<span style=\"color:#595959;font-size:15px;\">The&nbsp;</span><span style=\"font-size:15px;\"><span style=\"color:#595959;\">Siteand our mobile applicationmay contain (or you may be sent through the Siteor our mobile application) links</span></span><span style=\"color:#595959;font-size:15px;\">&nbsp;to other\r\nwebsites or content belonging to or originating from third parties or links to\r\nwebsites and features in banners or other advertising. Such external links are\r\nnot investigated, monitored, or checked for accuracy, adequacy, validity, reliability,\r\navailability or completeness by us. WE DO NOT WARRANT, ENDORSE, GUARANTEE, OR\r\nASSUME RESPONSIBILITY FOR THE ACCURACY OR RELIABILITY OF ANY INFORMATION\r\nOFFERED BY THIRD-PARTY WEBSITES LINKED THROUGH THE SITE OR ANY WEBSITE OR\r\nFEATURE LINKED IN ANY BANNER OR OTHER ADVERTISING. WE WILL NOT BE A PARTY TO OR\r\nIN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND THIRD-PARTY PROVIDERS OF PRODUCTS OR SERVICES.</span> \r\n</div>', 4, 1525667451, 1525667454, 1),
(8, 'Disclaimer', 'Disclaimer', '', '0', '1', 'Company Profile', '<p class=\"MsoNormal\">\r\n	The relevant provisions of the relevant ministries of the Peoples Bank noted in, bitcoin digital currency system and other special virtual goods as a commodity trading behavior on the Internet, ordinary people have the freedom to participate in the premise own risk. Currently there is a lot of uncertainty digital currency industry, uncontrollable risk factors (such as pre-dig, spike, making manipulation, the team disbanded, technical defects, etc.), resulting in trading is very risky.<span></span>Trading platform  only digital currency and other virtual goods enthusiasts to provide a free online exchange platform for the<span></span>Source Haitong digital network platform for the exchange of virtual currency and other commodities, the value of the site operator does not undertake any review, warranty, liability for compensation.\r\n</p>', 5, 1497495947, 1497495955, 1),
(9, 'Registration Agreement', 'Registration Agreement', '', '0', '1', 'aboutus', 'This agreement is made by and between you and operator of YourExchange and has the legal effect as a legal contract.<br />\r\n<br />\r\nThe operator of YourExchange means the legal entity that, recognized by law, operates the networking platform. Please refer to the company and license information at the bottom of the website of YourExchange for the information regarding the operator of YourExchange. The operator of YourExchange may be referred to, individually or collectively, as “YourExchange Limited” in this agreement. “YourExchange” means the networking platform operated by YourExchange, including but not limited to the YourExchange website, with the domain name of your.exchange, https://www.your.exchange, which is encrypted.<br />\r\n<br />\r\n1. Agreement and Execution<br />\r\n<br />\r\nThe content of this agreement includes main body of this agreement and various rules that have been posted or may be posted from time to time by YourExchange. All of the rules shall be an integral part of this agreement, and shall have the same legal effect as the main body of this agreement. Unless otherwise expressly provided, any service provided by YourExchange and its affiliates (hereinafter referred as “YourExchange Service”) shall be bound by this agreement. You shall carefully read through this agreement before using any YourExchange Service, and pay close attention to the content written in bold font. You may consult YourExchange if you have any question with regard to this agreement. However, regardless whether you have carefully read through this agreement before using YourExchange Service, you shall be bound by this agreement as long as you use YourExchange Service. You shall not claim to void or rescind this agreement on the ground that you did not read this agreement or you did not receive any respond from YourExchange to your consultation. You hereby promise to accept and observe this agreement. If you do not agree to this agreement, you shall immediately stop registration/activation or stop using YourExchange Service. YourExchange may make or amend this agreement and various rules from time to time as needed, and announce the same on the website, without any individual notice to you. The amended agreement and rules shall come into effect immediately and automatically upon being announced on the website. If you do not agree to the relevant amendment, you shall immediately stop using YourExchange Service. If you continue using YourExchange Service, you shall be deemed as having accepted the amended agreement and rules.<br />\r\n<br />\r\n2. Registration and Account<br />\r\n<br />\r\nEligibility of Registrants<br />\r\n<br />\r\nYou hereby confirm that you are an individual, legal person or other organization with full capacity for civil rights and civil conducts when you complete the registration or actually use YourExchange Service in any other way allowed by YourExchange. If you do not have the said capacity, you and your guardian shall undertake all the consequences resulted therefrom, and YourExchange shall have the right to cancel or permanently freeze your account, and claims against you and your guardian for compensation.<br />\r\n<br />\r\nRegistration and Account<br />\r\n<br />\r\nYou shall be bound by this agreement once you have filled in information, read and agreed to this agreement and completed the registration process following the instructions on the registration page or you have filled information, read and agreed to this agreement and completed the activation process following the instructions on the activation page, or upon your actual use of YourExchange Service in a way permitted by YourExchange. You may log in YourExchange by your email address or mobile number that you have provided or confirmed or any other means permitted by YourExchange. You must provide your real name, ID type, ID number and other information required by the laws and regulations. If any information you have provided during the registration is inaccurate, YourExchange will not take any responsibility and any loss, direct or indirect, and adverse consequence resulted therefrom will be borne by you. YourExchange accounts can only be used by the person whose name they are registered under. YourExchange reserves the right to suspend, freeze, or cancel accounts that are used by persons other than the persons whose names the accounts are registered under. YourExchange will also not take legal responsibility for these accounts.<br />\r\n<br />\r\nUser’s Information<br />\r\n<br />\r\nDuring the registration or activation, you shall accurately provide and timely update your information by following the instructions on the relevant page according to the laws and regulations in order to make it truthful, timely, complete and accurate. If there is any reasonable doubt that any information provided by you is wrong, untruthful, outdated or incomplete, YourExchange shall have the right to send you a notice to make enquiry and demand corrections, remove relevant information directly and, as the case may be, terminate all or part of YourExchange Service to you. YourExchange will not take any responsibility and any loss, direct or indirect, and adverse consequence resulted therefrom will be borne by you. You shall accurately fill in and timely update your email address, telephone number, contact address, postal code and other contact information so that YourExchange or any other user will be able to effectively contact you. You shall be solely and fully responsible for any loss or extra expenses incurred during the use of YourExchange Service by you if you cannot be contacted through these contact information. You hereby acknowledge and agree that you have the obligation to keep your contact information effective and to take actions as required by YourExchange if there is any change or update.<br />\r\n<br />\r\nAccount Security<br />\r\n<br />\r\nYou shall be solely responsible for the safekeeping of your YourExchange account and password on your own, and you shall be responsible for all activities under your log-in email, YourExchange account and password (including but not limited to information disclosure, information posting, consent to or submission of various rules and agreements by clicking on the website, online renewal of agreement or online purchase of services, etc.). You hereby agree that: a) you will notify YourExchange immediately if you are aware of any unauthorized use of your YourExchange account and password by any person or any other violations to the security rules; b) you will strictly observe the security, authentication, dealing, charging, withdrawal mechanism or procedures of the website/service; and c) you will log out the website by taking proper steps at the end of every visit. YourExchange shall not and will not be responsible for any loss caused by your failure to comply with this provision. You understand that YourExchange needs reasonable time to take actions upon your request, and YourExchange will not undertake any responsibility for the consequences (including but not limited to any of your loss) that have occurred prior to such actions.<br />\r\n<br />\r\n3. YourExchange Service<br />\r\n<br />\r\nThrough YourExchange Service and other services provided by YourExchange and its affiliates, members may post deal information, access to the pricing and dealing information of a deal and carry out the deal, participate in activities organized by YourExchange and enjoy other information services and technical services. If you have any dispute with other members arising from any transaction on YourExchange, once such dispute is submitted by one or both of you and the other member to YourExchange for dispute resolution, YourExchange shall have the right to make decision at its sole discretion. You hereby acknowledge and accept the discretion and decision of YourExchange. You acknowledge and agree that, YourExchange may, on requests from governmental authorities (including judicial and administrative departments), provide user information provided by you to YourExchange, transaction records and any other necessary information. If you allegedly infringe upon any other’s intellectual rights or other legitimate interests, YourExchange may provide the necessary ID information of you to the interest holder if YourExchange preliminarily decides that the infringement exists. All the applicable taxes and all the expenses in relation to hardware, software, service and etc. arising during your use of the YourExchange Service shall be solely borne by you. By using this service you accept that all trade executions are final and irreversible. By using this service you accept that YourExchange reserves the right to liquidate any trades at any time regardless of the profit or loss position.<br />\r\n<br />\r\n4. User’s Guide of YourExchange Service<br />\r\n<br />\r\nYou hereby promise to observe the following covenants during your use of YourExchange Service on YourExchange: All the activities that you carry out during the use of YourExchange Service will be in compliance with the requirements of laws, regulations, regulatory documents and various rules of YourExchange, will not be in violation of public interests, public ethnics or other’s legitimate interests, will not constitute evasion of payable taxes or fees and will not violate this agreement or relevant rules. If you violate the foregoing promises and thereby cause any legal consequence, you shall independently undertake all of the legal liabilities in your own name and hold YourExchange harmless from any loss resulted from such violation. During any transaction with other members, you will be in good faith, will not take any acts of unfair competition, will not disturb the normal order of online transactions, and will not engage in any acts unrelated to online transactions. You will not use any data on YourExchange for commercial purposes, including but not limited to using any data displayed on YourExchange through copy, dissemination or any other means without prior written consent of YourExchange. You will not use any device, software or subroutine to intervene or attempt to intervene the normal operation of YourExchange or any ongoing transaction or activities on YourExchange. You will not adopt any action that will induce unreasonable size of data loading on the network equipments of YourExchange. You acknowledge and agree: YourExchange shall have the right to unilaterally determine whether you have violated any of the covenants above and, according to such unilateral determination, apply relevant rules and take actions thereunder or terminate services to you, without your consent or prior notice to you. As required to maintain the order and security of transactions on YourExchange, YourExchange shall have the right to close relevant orders and take other actions in case of any malicious sale or purchase or any other events disturbing the normal order of transaction of the market. If your violation or infringement has been held by any effective legal documents issued by judicial or administrative authorities, or YourExchange determines at its sole discretion that it is likely that you have violated the terms of this agreement or the rules or the laws and regulations, YourExchange shall have the right to publish on YourExchange such alleged violations and the actions that having been taken against you by YourExchange. As to any information you may have published on YourExchange that allegedly violates or infringes upon the law, other’s legitimate interests or this agreement or the rules, YourExchange shall have the right to delete such information without any notice to you and impose punishments according to the rules. As to any act you may have carried out on YourExchange, including those you have not carried out on YourExchange but have had impacts on YourExchange and its users, YourExchange shall have the right to unilaterally determine its nature and whether it constitutes violation of this agreement or any rules, and impose punishments accordingly. You shall keep all the evidence related to your acts on your own and shall undertake all the adverse consequences resulted from your failure to discharge your burden of proof. If your alleged violation to your promises causes any losses to any third party, you shall solely undertake all the legal liabilities in your own name and hold YourExchange harmless from any loss or extra expenses. If, due to any alleged violation by you to the laws or this agreement, YourExchange incurs any losses, is claimed by any third party for compensation or suffers any punishment imposed by any administrative authorities, you shall indemnify YourExchange against any losses and expense caused thereby, including reasonable attorney’s fee.<br />\r\n<br />\r\n5. Scope and Limitation of Liability<br />\r\n<br />\r\nYourExchange will provide YourExchange Service at an “as is” and “commercially available” condition. YourExchange disclaims any express or implied warranty with regards to YourExchange Service, however, including but not limited to applicability, free from error or omission, continuity, accuracy, reliability or fitness for a particular purpose. Meanwhile, YourExchange disclaims any promise or warranty with regards to the effectiveness, accuracy, correctness, reliability, quality, stability, completeness and timeliness of the technology and information involved by YourExchange Service. You are fully aware that the information on YourExchange is published by users on their own and may contain risks and defects. YourExchange serves merely as a venue of transactions. YourExchange serves merely as a venue where you acquire coin related information, search for counterparties of transactions and negotiate and conduct transactions, but YourExchange cannot control the quality, security or legality of the coin involved in any transaction, truthfulness or accuracy of the transaction information, or capacity of the parties to any transaction to perform its obligations under the transaction documents. You shall cautiously make judgment on your own on the truthfulness, legality and effectiveness of the coin and information in question, and undertake any liabilities and losses that may be caused thereby. Unless expressly required by laws and regulations or any of the following circumstances occurs, YourExchange shall not have any duty to conduct preliminary review on information data, transaction activity and any other transaction related issues of all users: YourExchange has reasonable cause to suspect that a particular member and a particular transaction may materially violate the law or agreement. YourExchange has reasonable cause to suspect that the activities conducted on YourExchange by a member may be illegal or improper. You acknowledge and agree, YourExchange shall not be liable for any of your losses caused by any of the following events, including but not limited to losses of profits, goodwill, usage or data or any other intangible losses (regardless whether YourExchange has been advised of the possibility of such losses): use or failure to use YourExchange Service. unauthorized use of your account or unauthorized alternation of your data by any third parties. expenses and losses incurred from purchase or acquisition of any data or information or engagement in transaction through YourExchange Service, or any alternatives of the same. your misunderstanding on YourExchange Service. any other losses related to YourExchange Service which are not attributable to YourExchange. In no event shall YourExchange be liable for any failure or delay of service resulted from regular equipment maintenance of the information network, connection error of information network, error of computers, communication or other systems, power failure, strike, labor disputes, riots, revolutions, chaos, insufficiency of production or materials, fire, flood, tornado, blast, war, governmental acts or judicial orders. You agree to indemnify and hold harmless YourExchange, its contractors, and its licensors, and their respective directors, officers, employees and agents from and against any and all claims and expenses, including attorneys’ fees, arising out of your use of the Website, including but not limited to out of your violation this Agreement.<br />\r\n<br />\r\n6. Termination of Agreement<br />\r\n<br />\r\nYou hereby agree that, YourExchange shall have the right to terminate all or part of YourExchange Service to you, temporarily freeze or permanently freeze (cancel) the authorizations of your account on YourExchange at YourExchange’s sole discretion, without any prior notice, for whatsoever reason, and YourExchange shall not be liable to you; however, YourExchange shall have the right to keep and use the transaction data, records and other information that is related to such account. In case of any of the following events, YourExchange shall have the right to directly terminate this agreement by cancelling your account, and shall have the right to permanently freeze (cancel) the authorizations of your account on YourExchange and withdraw the corresponding YourExchange account thereof: after YourExchange terminates services to you, you allegedly register or register in any other person’s name as YourExchange user again, directly or indirectly; the main content of user’s information that you have provided is untruthful, inaccurate, outdated or incomplete; when this agreement (including the rules) is amended, you expressly state and notify YourExchange of your unwillingness to accept the amended service agreement; any other circumstances where YourExchange deems it should terminate the services. After the account service is terminated or the authorizations of your account on YourExchange is permanently froze (cancelled), YourExchange shall not have any duty to keep or disclose to you any information in your account or forward any information you have not read or sent to you or any third party. You agree that, after the termination of agreement between you and YourExchange, YourExchange shall still have the rights to: keep your user’s information and all the transaction information during your use of YourExchange Service. Claim against you according to this agreement if you have violated any laws, this agreement or the rules during your use of YourExchange Service. After YourExchange suspends or terminates YourExchange Service to you, your transaction activities prior to such suspension or termination will be dealt with according to the following principles and you shall will take care of on your own efforts and fully undertake any disputes, losses or extra expenses caused thereby and keep YourExchange harmless from any losses or expenses: YourExchange shall have the right to delete, at the same time of suspension or termination of services, information related to any un-traded coin tokens that you have uploaded to YourExchange prior to the suspension or termination. If you have reached any purchase agreement with any other member prior to the suspension or termination but such agreement has not been actually performed, YourExchange shall have the right to delete information related to such purchase agreement and the coins in question. If you have reached any purchase agreement with any other member prior to the suspension or termination and such agreement has been partially performed, YourExchange may elect not to delete the transaction; provided, however, YourExchange shall have the right to notify your counterparty of the situation at the same time of the suspension or termination.<br />\r\n<br />\r\n7. Privacy Policy<br />\r\n<br />\r\nYourExchange may announce and amend its privacy policy on the platform of YourExchange from time to time and the privacy policy shall be an integral part of this agreement.<br />\r\n<br />', 6, 1523761518, 1523761522, 1),
(10, 'Registration Guide', 'Registration Guide', '', '0', '1', 'help', '<img src=\"/Upload/article/583a700024ba4.png\" alt=\"\" />', 1, 1497495861, 1497495865, 1),
(11, 'Trading Guide', 'Trading Guide', '', '0', '1', 'help', '', 2, 1497495802, 1497495805, 1),
(12, 'Recharge Guide', 'Recharge Guide', '', '0', '1', 'help', '', 3, 1497495770, 1497495773, 1),
(13, 'Recharge limit', 'Recharge limit', '', '0', '1', 'help', 'Minimum recharge $100 Maximum recharge $5000', 4, 1497495698, 1497495701, 1),
(14, 'Withdraw Guide', 'Withdraw Guide', '', '0', '1', 'help', '<h3 style=\"font-family:sans-serif;font-weight:500;font-size:24px;background-color:#FFFFFF;\">\r\n	Withdraw Notice\r\n</h3>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	1. Withdrawal fee rate 1%, Minimum charge per withdrawal $2.\r\n</p>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	2. Single cash withdrawal limit $100--$50000.\r\n</p>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	3. Bank card withdrawal24Arrive within hours, it has been exported 24 hours After payment has not received , please contact customer service.\r\n</p>', 5, 1497495645, 1497495649, 1),
(19, 'aaa', 'Announcement', '', '1', '0', '', '<img src=\"/Upload/article/5955b7dbec138.png\" alt=\"\" />', 2, 1497456000, 1497456000, 1),
(20, 'bbb', 'Industry News', '', '1', '0', '', '', 3, 1497456000, 1497456000, 1),
(21, 'Mining', 'Mining', '', '1', '0', '', '', 4, 1497493937, 1497493942, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_auth_extend`
--

DROP TABLE IF EXISTS `codono_auth_extend`;
CREATE TABLE IF NOT EXISTS `codono_auth_extend` (
  `group_id` mediumint(10) UNSIGNED NOT NULL COMMENT 'userid',
  `extend_id` mediumint(8) UNSIGNED NOT NULL COMMENT 'Extension data tableid',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT 'Extended type identifier 1:Column classification authority;2:Permissions model',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_auth_extend`
--

INSERT INTO `codono_auth_extend` (`group_id`, `extend_id`, `type`) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 2),
(1, 3, 1),
(1, 3, 2),
(1, 4, 1),
(1, 37, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_auth_group`
--

DROP TABLE IF EXISTS `codono_auth_group`;
CREATE TABLE IF NOT EXISTS `codono_auth_group` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'user groupid,Auto-increment primary keys',
  `module` varchar(20) NOT NULL COMMENT 'User group module',
  `type` tinyint(4) NOT NULL COMMENT 'Group Type',
  `title` char(20) NOT NULL DEFAULT '' COMMENT 'User Group Chinese name',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT 'Description',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'User group status: is1Normal for the0Disable,-1For deletion',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT 'User groups have rulesid, More rules , Apart',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_auth_group`
--

INSERT INTO `codono_auth_group` (`id`, `module`, `type`, `title`, `description`, `status`, `rules`) VALUES
(2, 'admin', 1, 'Financial Management', 'With the relevant authority websites funds', -1, '431'),
(3, 'admin', 1, 'Super Admin', 'All permissions', 1, '1917,1948,1949,1995,2007,2032,2046,2047,2048,2049,2050,2051,2052,2053,2054,2056,2057,2058,2059,2060,2061,2062,2063,2064,2065,2066,2067,2068,2069,2070,2071,2072,2073,2074,2075,2076,2077,2078,2079,2080,2081,2082,2083,2084,2085,2086,2088,2091,2092,2093,2094,2095,2097,2098,2099,2100,2101,2102,2103,2104,2105,2106,2107,2110,2111,2112,2113,2114,2115,2116,2117,2118,2119,2120'),
(5, 'admin', 1, 'Information Administ', 'News articles have a website permissions', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `codono_auth_group_access`
--

DROP TABLE IF EXISTS `codono_auth_group_access`;
CREATE TABLE IF NOT EXISTS `codono_auth_group_access` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'userid',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT 'user groupid',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_auth_group_access`
--

INSERT INTO `codono_auth_group_access` (`uid`, `group_id`) VALUES
(1, 11),
(2, 13),
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `codono_auth_rule`
--

DROP TABLE IF EXISTS `codono_auth_rule`;
CREATE TABLE IF NOT EXISTS `codono_auth_rule` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ruleid,Auto-increment primary keys',
  `module` varchar(20) NOT NULL COMMENT 'Rule belongsmodule',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-main menu',
  `name` char(80) NOT NULL DEFAULT '' COMMENT 'The only rules of English identity',
  `title` char(20) NOT NULL DEFAULT '' COMMENT 'Rule description',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'is it effective(0:invalid,1:effective)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT 'Rules additional conditions',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=2121 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_auth_rule`
--

INSERT INTO `codono_auth_rule` (`id`, `module`, `type`, `name`, `title`, `status`, `condition`) VALUES
(425, 'admin', 1, 'Admin/article/add', 'New', -1, ''),
(427, 'admin', 1, 'Admin/article/setStatus', 'Change state', -1, ''),
(428, 'admin', 1, 'Admin/article/update', 'Storage', -1, ''),
(429, 'admin', 1, 'Admin/article/autoSave', 'save draft', -1, ''),
(430, 'admin', 1, 'Admin/article/move', 'mobile', -1, ''),
(432, 'admin', 2, 'Admin/Article/mydocument', 'content', -1, ''),
(437, 'admin', 1, 'Admin/Trade/config', 'Trading Configuratio', -1, ''),
(449, 'admin', 1, 'Admin/Index/operate', 'Market Statistics', -1, ''),
(455, 'admin', 1, 'Admin/Issue/config', 'ICO Configu', -1, ''),
(457, 'admin', 1, 'Admin/Index/database/type/export', 'data backup', -1, ''),
(461, 'admin', 1, 'Admin/Article/chat', 'Chat list', -1, ''),
(464, 'admin', 1, 'Admin/Index/database/type/import', 'Data Restore', -1, ''),
(471, 'admin', 1, 'Admin/Mytx/config', 'Withdraw Configurati', -1, ''),
(472, 'admin', 2, 'Admin/Mytx/index', 'withdraw', -1, ''),
(473, 'admin', 1, 'Admin/Config/market', 'Market allocation', -1, ''),
(477, 'admin', 1, 'Admin/User/myzr', 'Into virtual currenc', -1, ''),
(479, 'admin', 1, 'Admin/User/myzc', 'Out of virtual curre', -1, ''),
(482, 'admin', 2, 'Admin/ExtA/index', 'Spread', -1, ''),
(488, 'admin', 1, 'Admin/Auth_manager/createGroup', 'New User Group', -1, ''),
(499, 'admin', 1, 'Admin/ExtA/index', 'Extension Manager', -1, ''),
(509, 'admin', 1, 'Admin/Article/adver_edit', 'edit', -1, ''),
(510, 'admin', 1, 'Admin/Article/adver_status', 'modify', -1, ''),
(513, 'admin', 1, 'Admin/Issue/index_edit', 'Edit ICO', -1, ''),
(514, 'admin', 1, 'Admin/Issue/index_status', 'Modify ICO', -1, ''),
(515, 'admin', 1, 'Admin/Article/chat_edit', 'edit', -1, ''),
(516, 'admin', 1, 'Admin/Article/chat_status', 'modify', -1, ''),
(517, 'admin', 1, 'Admin/User/coin_edit', 'coinmodify', -1, ''),
(519, 'admin', 1, 'Admin/Mycz/type_status', 'Modify status', -1, ''),
(520, 'admin', 1, 'Admin/Issue/log_status', 'ICO status', -1, ''),
(521, 'admin', 1, 'Admin/Issue/log_jiedong', 'ICO thaw', -1, ''),
(522, 'admin', 1, 'Admin/Tools/database/type/export', 'data backup', -1, ''),
(525, 'admin', 1, 'Admin/Config/coin_edit', 'edit', -1, ''),
(526, 'admin', 1, 'Admin/Config/coin_add', 'Edit currency', -1, ''),
(527, 'admin', 1, 'Admin/Config/coin_status', 'Modify status', -1, ''),
(528, 'admin', 1, 'Admin/Config/market_edit', 'edit', -1, ''),
(530, 'admin', 1, 'Admin/Tools/database/type/import', 'Data Restore', -1, ''),
(541, 'admin', 2, 'Admin/Trade/config', 'transaction', -1, ''),
(569, 'admin', 1, 'Admin/ADVERstatus', 'modify', -1, ''),
(570, 'admin', 1, 'Admin/Tradelog/index', 'Transaction Record', -1, ''),
(585, 'admin', 1, 'Admin/Config/mycz', 'Recharge Configurati', -1, ''),
(590, 'admin', 1, 'Admin/Mycztype/index', 'Recharge type', -1, ''),
(600, 'admin', 1, 'Admin/Usergoods/index', 'User Address', -1, ''),
(1379, 'admin', 1, 'Admin/Bazaar/index', 'Market Management', -1, ''),
(1405, 'admin', 1, 'Admin/Bazaar/config', 'Bazaar Configuration', -1, ''),
(1425, 'admin', 1, 'Admin/Bazaar/log', 'Record Mart', -1, ''),
(1451, 'admin', 1, 'Admin/Bazaar/invit', 'Market Promotion', -1, ''),
(1846, 'admin', 1, 'Admin/AuthManager/createGroup', 'New User Group', -1, ''),
(1847, 'admin', 1, 'Admin/AuthManager/editgroup', 'Edit User Groups', -1, ''),
(1848, 'admin', 1, 'Admin/AuthManager/writeGroup', 'Update User Group', -1, ''),
(1849, 'admin', 1, 'Admin/AuthManager/changeStatus', 'Change state', -1, ''),
(1850, 'admin', 1, 'Admin/AuthManager/access', 'Access authorization', -1, ''),
(1851, 'admin', 1, 'Admin/AuthManager/category', 'Classification Autho', -1, ''),
(1852, 'admin', 1, 'Admin/AuthManager/user', 'Members of the autho', -1, ''),
(1853, 'admin', 1, 'Admin/AuthManager/tree', 'Members of the list', -1, ''),
(1854, 'admin', 1, 'Admin/AuthManager/group', 'user group', -1, ''),
(1855, 'admin', 1, 'Admin/AuthManager/addToGroup', 'Added to the user gr', -1, ''),
(1856, 'admin', 1, 'Admin/AuthManager/removeFromGroup', 'User group removed', -1, ''),
(1857, 'admin', 1, 'Admin/AuthManager/addToCategory', 'Classified added to', -1, ''),
(1858, 'admin', 1, 'Admin/AuthManager/addToModel', 'Model added to the u', -1, ''),
(1859, 'admin', 1, 'Admin/Trade/status', 'Modify status', -1, ''),
(1860, 'admin', 1, 'Admin/Trade/reject', 'Revoked pending', -1, ''),
(1862, 'admin', 1, 'Admin/Login/index', 'User login', -1, ''),
(1863, 'admin', 1, 'Admin/Login/loginout', 'User exits', -1, ''),
(1864, 'admin', 1, 'Admin/User/setpwd', 'Change the administr', -1, ''),
(1877, 'admin', 1, 'Admin/Article/edit', 'Edit Add', -1, ''),
(1878, 'admin', 1, 'Admin/Text/index', 'Text Tips', -1, ''),
(1879, 'admin', 1, 'Admin/Text/edit', 'edit', -1, ''),
(1880, 'admin', 1, 'Admin/Text/status', 'modify', -1, ''),
(1882, 'admin', 1, 'Admin/User/config', 'User Configuration', -1, ''),
(1884, 'admin', 1, 'Admin/Finance/myczTypeEdit', 'Edit Add', -1, ''),
(1885, 'admin', 1, 'Admin/Finance/config', 'Configuration', -1, ''),
(1887, 'admin', 1, 'Admin/Finance/type', 'Types of', -1, ''),
(1888, 'admin', 1, 'Admin/Finance/type_status', 'Modify status', -1, ''),
(1889, 'admin', 1, 'Admin/User/edit', 'Edit Add', -1, ''),
(1890, 'admin', 1, 'Admin/User/status', 'Modify status', -1, ''),
(1891, 'admin', 1, 'Admin/User/adminEdit', 'Edit Add', -1, ''),
(1892, 'admin', 1, 'Admin/User/adminStatus', 'Modify status', -1, ''),
(1893, 'admin', 1, 'Admin/User/authEdit', 'Edit Add', -1, ''),
(1894, 'admin', 1, 'Admin/User/authStatus', 'Modify status', -1, ''),
(1895, 'admin', 1, 'Admin/User/authStart', 'Permission to re-ini', -1, ''),
(1896, 'admin', 1, 'Admin/User/logEdit', 'Edit Add', -1, ''),
(1897, 'admin', 1, 'Admin/User/logStatus', 'Modify status', -1, ''),
(1898, 'admin', 1, 'Admin/User/walletEdit', 'Edit Add', -1, ''),
(1900, 'admin', 1, 'Admin/User/walletStatus', 'Modify status', -1, ''),
(1901, 'admin', 1, 'Admin/User/bankEdit', 'Edit Add', -1, ''),
(1902, 'admin', 1, 'Admin/User/bankStatus', 'Modify status', -1, ''),
(1903, 'admin', 1, 'Admin/User/coinEdit', 'Edit Add', -1, ''),
(1904, 'admin', 1, 'Admin/User/coinLog', 'Property statistics', -1, ''),
(1905, 'admin', 1, 'Admin/User/goodsEdit', 'Edit Add', -1, ''),
(1906, 'admin', 1, 'Admin/User/goodsStatus', 'Modify status', -1, ''),
(1907, 'admin', 1, 'Admin/Article/typeEdit', 'Edit Add', -1, ''),
(1908, 'admin', 1, 'Admin/Article/linkEdit', 'Edit Add', -1, ''),
(1910, 'admin', 1, 'Admin/Article/adverEdit', 'Edit Add', -1, ''),
(1911, 'admin', 1, 'Admin/User/authAccess', 'Access authorization', -1, ''),
(1912, 'admin', 1, 'Admin/User/authAccessUp', 'Access unauthorized', -1, ''),
(1913, 'admin', 1, 'Admin/User/authUser', 'Members of the autho', -1, ''),
(1914, 'admin', 1, 'Admin/User/authUserAdd', 'Members of the autho', -1, ''),
(1915, 'admin', 1, 'Admin/User/authUserRemove', 'Members of the autho', -1, ''),
(1917, 'admin', 1, 'Admin/App/config', 'APP Config', 1, ''),
(1918, 'admin', 1, 'AdminUser/detail', 'User Details backgro', -1, ''),
(1919, 'admin', 1, 'AdminUser/status', 'Background user stat', -1, ''),
(1920, 'admin', 1, 'AdminUser/add', 'New user background', -1, ''),
(1921, 'admin', 1, 'AdminUser/edit', 'Users to edit the ba', -1, ''),
(1922, 'admin', 1, 'Admin/Articletype/edit', 'edit', -1, ''),
(1924, 'admin', 1, 'Admin/Topup/index', 'Recharge record', -1, ''),
(1925, 'admin', 1, 'Admin/Topup/config', 'Recharge Configurati', -1, ''),
(1928, 'admin', 1, 'Admin/Money/index', 'Money Management', -1, ''),
(1931, 'admin', 1, 'Admin/Article/images', 'upload image', -1, ''),
(1932, 'admin', 1, 'Admin/Adver/edit', 'edit', -1, ''),
(1933, 'admin', 1, 'Admin/Adver/status', 'modify', -1, ''),
(1935, 'admin', 1, 'Admin/User/index_edit', 'edit', -1, ''),
(1936, 'admin', 1, 'Admin/User/index_status', 'modify', -1, ''),
(1938, 'admin', 1, 'Admin/Finance/myczTypeStatus', 'Modify status', -1, ''),
(1939, 'admin', 1, 'Admin/Finance/myczTypeImage', 'upload image', -1, ''),
(1940, 'admin', 1, 'Admin/Finance/mytxStatus', 'Modify status', -1, ''),
(1941, 'admin', 1, 'Admin/Tools/dataExport', 'backup database', -1, ''),
(1942, 'admin', 1, 'Admin/Tools/dataImport', 'Restore Database', -1, ''),
(1945, 'admin', 1, 'Admin/Issue/edit', 'Edit ICO', -1, ''),
(1946, 'admin', 1, 'Admin/Issue/status', 'Modify ICO', -1, ''),
(1948, 'admin', 1, 'Admin/Invit/config', 'Promotion', 1, ''),
(1949, 'admin', 1, 'Admin/App/vip_config_list', 'APP VIP', 1, ''),
(1950, 'admin', 1, 'Admin/Link/edit', 'edit', -1, ''),
(1951, 'admin', 1, 'Admin/Link/status', 'modify', -1, ''),
(1954, 'admin', 1, 'Admin/Money/log', 'Money Log', -1, ''),
(1956, 'admin', 1, 'Admin/Chat/edit', 'edit', -1, ''),
(1957, 'admin', 1, 'Admin/Chat/status', 'modify', -1, ''),
(1961, 'admin', 1, 'Admin/Usercoin/edit', 'Modify property', -1, ''),
(1962, 'admin', 1, 'Admin/Finance/mytxExcel', 'Export selected', -1, ''),
(1964, 'admin', 1, 'Admin/Mycz/status', 'modify', -1, ''),
(1965, 'admin', 1, 'Admin/Mycztype/status', 'Modify status', -1, ''),
(1967, 'admin', 1, 'Admin/App/adsblock_list', 'APPAdvertising secto', -1, ''),
(1969, 'admin', 1, 'Admin/Tools/wallet', 'Check the wallet', -1, ''),
(1972, 'admin', 1, 'Admin/Topup/type', 'Recharge amount', -1, ''),
(1973, 'admin', 1, 'Admin/Money/fee', 'Financial details', -1, ''),
(1977, 'admin', 1, 'Admin/Finance/mytxChuli', 'Processing', -1, ''),
(1979, 'admin', 1, 'Admin/Config/bank_edit', 'edit', -1, ''),
(1981, 'admin', 1, 'Admin/Coin/status', 'Modify status', -1, ''),
(1983, 'admin', 1, 'Admin/Config/market_add', 'Modify status', -1, ''),
(1984, 'admin', 1, 'Admin/Tools/invoke', 'Other module calls', -1, ''),
(1985, 'admin', 1, 'Admin/Tools/optimize', 'Table Optimization', -1, ''),
(1986, 'admin', 1, 'Admin/Tools/repair', 'Repair Tables', -1, ''),
(1987, 'admin', 1, 'Admin/Tools/del', 'Removing Backup File', -1, ''),
(1988, 'admin', 1, 'Admin/Tools/export', 'backup database', -1, ''),
(1989, 'admin', 1, 'Admin/Tools/import', 'Restore Database', -1, ''),
(1990, 'admin', 1, 'Admin/Tools/excel', 'Export Database', -1, ''),
(1991, 'admin', 1, 'Admin/Tools/exportExcel', 'ExportExcel', -1, ''),
(1992, 'admin', 1, 'Admin/Tools/importExecl', 'ImportingExcel', -1, ''),
(1994, 'admin', 1, 'Admin/User/detail', 'User Details', -1, ''),
(1995, 'admin', 1, 'Admin/App/ads_user', 'APP Ads', 1, ''),
(1998, 'admin', 1, 'Admin/Topup/coin', 'payment method', -1, ''),
(2003, 'admin', 1, 'Admin/Finance/mytxReject', 'Undo withdrawals', -1, ''),
(2004, 'admin', 1, 'Admin/Mytx/status', 'Modify status', -1, ''),
(2005, 'admin', 1, 'Admin/Mytx/excel', 'cancel', -1, ''),
(2006, 'admin', 1, 'Admin/Mytx/exportExcel', 'Importingexcel', -1, ''),
(2007, 'admin', 1, 'Admin/Menu/index', 'Menu Manager', 1, ''),
(2016, 'admin', 1, 'Admin/Menu/importFile', 'Import File', -1, ''),
(2017, 'admin', 1, 'Admin/Menu/import', 'Importing', -1, ''),
(2024, 'admin', 1, 'Admin/Finance/mytxConfirm', 'Confirm Withdraw', -1, ''),
(2025, 'admin', 1, 'Admin/Finance/myzcConfirm', 'Confirm turn out', -1, ''),
(2030, 'admin', 1, 'Admin/Verify/code', 'Captcha', -1, ''),
(2031, 'admin', 1, 'Admin/Verify/mobile', 'Phone code', -1, ''),
(2032, 'admin', 1, 'Admin/Verify/email', 'Mail Code', 1, ''),
(2035, 'admin', 1, 'Admin/User/myzc_qr', 'Confirm turn out', -1, ''),
(2036, 'admin', 1, 'Admin/Article/status', 'Modify status', -1, ''),
(2037, 'admin', 1, 'Admin/Finance/myczStatus', 'Modify status', -1, ''),
(2038, 'admin', 1, 'Admin/Finance/myczConfirm', 'Confirm arrival', -1, ''),
(2039, 'admin', 1, 'Admin/Article/typeStatus', 'Modify status', -1, ''),
(2040, 'admin', 1, 'Admin/Article/linkStatus', 'Modify status', -1, ''),
(2041, 'admin', 1, 'Admin/Article/adverStatus', 'Modify status', -1, ''),
(2042, 'admin', 1, 'Admin/Article/adverImage', 'upload image', -1, ''),
(2045, 'admin', 1, 'Admin/Shop/images', 'image', 1, ''),
(2046, 'admin', 2, 'Admin/Index/index', 'Dashboard', 1, ''),
(2047, 'admin', 2, 'Admin/Article/index', 'Content', 1, ''),
(2048, 'admin', 2, 'Admin/User/index', 'User', 1, ''),
(2049, 'admin', 2, 'Admin/Finance/index', 'Finance', 1, ''),
(2050, 'admin', 2, 'Admin/Trade/index', 'Trade', 1, ''),
(2051, 'admin', 2, 'Admin/Game/index', 'ICO', 1, ''),
(2052, 'admin', 2, 'Admin/Config/index', 'Config', 1, ''),
(2053, 'admin', 2, 'Admin/Operate/index', 'System', -1, ''),
(2054, 'admin', 2, 'Admin/Tools/index', 'Tools', 1, ''),
(2055, 'admin', 2, 'Admin/Cloud/index', 'Cloud', -1, ''),
(2056, 'admin', 1, 'Admin/Index/index', 'Dashboard', 1, ''),
(2057, 'admin', 1, 'Admin/Article/index', 'Articles', 1, ''),
(2058, 'admin', 1, 'Admin/User/index', 'Users', 1, ''),
(2059, 'admin', 1, 'Admin/Trade/index', 'Trade', 1, ''),
(2060, 'admin', 1, 'Admin/Config/index', 'Basic', 1, ''),
(2061, 'admin', 1, 'Admin/Finance/index', 'Financial details', 1, ''),
(2062, 'admin', 1, 'Admin/Tools/index', 'Clear the cache', 1, ''),
(2063, 'admin', 1, 'Admin/Operate/index', 'Promotion award', 1, ''),
(2064, 'admin', 1, 'Admin/Shop/index', 'Products', 1, ''),
(2065, 'admin', 1, 'Admin/Vote/index', 'Voting Record', 1, ''),
(2066, 'admin', 1, 'Admin/Vote/type', 'Voting type', 1, ''),
(2067, 'admin', 1, 'Admin/Issue/index', 'ICO', 1, ''),
(2068, 'admin', 1, 'Admin/Issue/log', 'Records', 1, ''),
(2069, 'admin', 1, 'Admin/User/award', 'Award', 1, ''),
(2070, 'admin', 1, 'Admin/Trade/log', 'Logs', 1, ''),
(2071, 'admin', 1, 'Admin/Config/cellphone', 'SMS', 1, ''),
(2072, 'admin', 1, 'Admin/Index/coin', 'Coin stats', 1, ''),
(2073, 'admin', 1, 'Admin/Article/type', 'Categories', 1, ''),
(2074, 'admin', 1, 'Admin/Finance/mycz', 'Fiat Deposit', 1, ''),
(2075, 'admin', 1, 'Admin/User/admin', 'Admins', 1, ''),
(2076, 'admin', 1, 'Admin/Shop/config', 'Config', 1, ''),
(2077, 'admin', 1, 'Admin/Index/market', 'Market', 1, ''),
(2078, 'admin', 1, 'Admin/Config/contact', 'Support', 1, ''),
(2079, 'admin', 1, 'Admin/Tools/queue', 'Server queue', 1, ''),
(2080, 'admin', 1, 'Admin/Article/adver', 'Slider', 1, ''),
(2081, 'admin', 1, 'Admin/Trade/chat', 'Chat', 1, ''),
(2082, 'admin', 1, 'Admin/Finance/myczType', 'Payment Gateways', 1, ''),
(2083, 'admin', 1, 'Admin/User/auth', 'Permissions', 1, ''),
(2084, 'admin', 1, 'Admin/App/ads_list/block_id/1', 'WAP Banners', 1, ''),
(2085, 'admin', 1, 'Admin/Shop/type', 'Categories', 1, ''),
(2086, 'admin', 1, 'Admin/Dividend/index', 'Dividend', 1, ''),
(2087, 'admin', 1, 'Admin/Cloud/game', 'Application', -1, ''),
(2088, 'admin', 1, 'Admin/Config/bank', 'Bank', 1, ''),
(2089, 'admin', 1, 'Admin/Coin/edit', 'edit', 1, ''),
(2090, 'admin', 1, 'Admin/Market/edit', 'Editing Market', 1, ''),
(2091, 'admin', 1, 'Admin/Trade/comment', 'Coin Reviews', 1, ''),
(2092, 'admin', 1, 'Admin/Article/link', 'Footer Links', 1, ''),
(2093, 'admin', 1, 'Admin/User/log', 'Signin Log', 1, ''),
(2094, 'admin', 1, 'Admin/Finance/mytx', 'Fiat Withdrawal', 1, ''),
(2095, 'admin', 1, 'Admin/Config/coin', 'Coin', 1, ''),
(2096, 'admin', 1, 'Admin/Cloud/theme', 'Skin', -1, ''),
(2097, 'admin', 1, 'Admin/Shop/coin', 'Payment method', 1, ''),
(2098, 'admin', 1, 'Admin/Menu/sort', 'Sequence', 1, ''),
(2099, 'admin', 1, 'Admin/Menu/add', 'Add to', 1, ''),
(2100, 'admin', 1, 'Admin/Menu/edit', 'edit', 1, ''),
(2101, 'admin', 1, 'Admin/Menu/del', 'delete', 1, ''),
(2102, 'admin', 1, 'Admin/Menu/toogleHide', 'Whether to hide', 1, ''),
(2103, 'admin', 1, 'Admin/Menu/toogleDev', 'Whether development', 1, ''),
(2104, 'admin', 1, 'Admin/Config/text', 'Tips', 1, ''),
(2105, 'admin', 1, 'Admin/User/wallet', 'Users wallet', 1, ''),
(2106, 'admin', 1, 'Admin/Trade/market', 'Market', 1, ''),
(2107, 'admin', 1, 'Admin/Finance/mytxConfig', 'Fiat Config', 1, ''),
(2108, 'admin', 1, 'Admin/Cloud/sidekick', 'Customer Service', -1, ''),
(2109, 'admin', 1, 'Admin/Cloud/sidekickUp', 'use', -1, ''),
(2110, 'admin', 1, 'Admin/Shop/log', 'Orders', 1, ''),
(2111, 'admin', 1, 'Admin/Dividend/log', 'Records', 1, ''),
(2112, 'admin', 1, 'Admin/Config/misc', 'Misc', 1, ''),
(2113, 'admin', 1, 'Admin/User/bank', 'Withdraw Address', 1, ''),
(2114, 'admin', 1, 'Admin/Trade/invit', 'Invite', 1, ''),
(2115, 'admin', 1, 'Admin/Finance/myzr', 'Crypto Deposit', 1, ''),
(2116, 'admin', 1, 'Admin/Shop/goods', 'Shipping address', 1, ''),
(2117, 'admin', 1, 'Admin/User/coin', 'User Coins', 1, ''),
(2118, 'admin', 1, 'Admin/Finance/myzc', 'Crypto Withdraw', 1, ''),
(2119, 'admin', 1, 'Admin/Config/navigation', 'Menu', 1, ''),
(2120, 'admin', 1, 'Admin/User/goods', 'Address', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `codono_bazaar`
--

DROP TABLE IF EXISTS `codono_bazaar`;
CREATE TABLE IF NOT EXISTS `codono_bazaar` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `coin` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `deal` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Market transaction table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_bazaar_config`
--

DROP TABLE IF EXISTS `codono_bazaar_config`;
CREATE TABLE IF NOT EXISTS `codono_bazaar_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `market` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Market Name',
  `price_min` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The minimum transaction price',
  `price_max` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The maximum transaction price',
  `num_mix` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The minimum number of transactions',
  `num_max` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The maximum number of transactions',
  `invit_coin` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Presented on the home currency',
  `invit_1` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The proportion of a generation gift',
  `invit_2` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The proportion of second-generation gift',
  `invit_3` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Three generations proportion gift',
  `fee` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Fees',
  `default` tinyint(2) UNSIGNED NOT NULL COMMENT 'default',
  `sort` int(11) UNSIGNED NOT NULL COMMENT 'Sequence',
  `addtime` int(11) UNSIGNED NOT NULL COMMENT 'add time',
  `endtime` int(11) UNSIGNED NOT NULL COMMENT 'Edit time',
  `status` tinyint(2) UNSIGNED NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `coinname` (`market`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `codono_bazaar_invit`
--

DROP TABLE IF EXISTS `codono_bazaar_invit`;
CREATE TABLE IF NOT EXISTS `codono_bazaar_invit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `invit` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `codono_bazaar_log`
--

DROP TABLE IF EXISTS `codono_bazaar_log`;
CREATE TABLE IF NOT EXISTS `codono_bazaar_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `peerid` int(11) UNSIGNED DEFAULT NULL,
  `coin` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`),
  KEY `peerid` (`peerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Market transactions table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_category`
--

DROP TABLE IF EXISTS `codono_category`;
CREATE TABLE IF NOT EXISTS `codono_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'classificationID',
  `name` varchar(30) NOT NULL COMMENT 'Mark',
  `title` varchar(50) NOT NULL COMMENT 'title',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Sub-headingsID',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Sort (effectively the same level)',
  `list_row` tinyint(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT 'List the number of lines per page',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEOThe page title',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'Keyword',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'description',
  `template_index` varchar(100) NOT NULL COMMENT 'Channel page template',
  `template_lists` varchar(100) NOT NULL COMMENT 'List Template',
  `template_detail` varchar(100) NOT NULL COMMENT 'Details page template',
  `template_edit` varchar(100) NOT NULL COMMENT 'Edit page template',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT 'Relational Model',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT 'Allow the type of content published',
  `link_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Outside the chain',
  `allow_publish` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Yes No Allowed to publish content',
  `display` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visibility',
  `reply` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Yes No Reply allowed',
  `check` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Whether to publish the article needs to be reviewed',
  `reply_model` varchar(100) NOT NULL DEFAULT '',
  `extend` text NOT NULL COMMENT 'Extended Setup',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Created',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Updated',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Data status',
  `icon` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Category Icon',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Category Table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_category`
--

INSERT INTO `codono_category` (`id`, `name`, `title`, `pid`, `sort`, `list_row`, `meta_title`, `keywords`, `description`, `template_index`, `template_lists`, `template_detail`, `template_edit`, `model`, `type`, `link_id`, `allow_publish`, `display`, `reply`, `check`, `reply_model`, `extend`, `create_time`, `update_time`, `status`, `icon`) VALUES
(1, 'blog', 'Blog', 0, 0, 10, '', '', '', '', '', '', '', '2', '2,1', 0, 0, 1, 0, 0, '1', '', 1379474947, 1382701539, 1, 0),
(2, 'default_blog', 'default category', 1, 1, 10, '', '', '', '', '', '', '', '2', '2,1,3', 0, 1, 1, 0, 1, '1', '', 1379475028, 1386839751, 1, 31);

-- --------------------------------------------------------

--
-- Table structure for table `codono_chat`
--

DROP TABLE IF EXISTS `codono_chat`;
CREATE TABLE IF NOT EXISTS `codono_chat` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` varchar(20) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 COMMENT='Text chat table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_chat`
--

INSERT INTO `codono_chat` (`id`, `userid`, `username`, `content`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, '19', 'mancore', 'trollme', 0, 1520639118, 1520621118, 1),
(2, '19', 'mancore', '[/#66]', 0, 1520639229, 1520621229, 1),
(3, '19', 'mancore', 'Ola\n', 0, 1520639744, 1520621744, 1),
(4, '19', 'mancore', 'This is quick test\n', 0, 1520639755, 1520621755, 1),
(5, '19', 'mancore', '@mancore :hyy\n', 0, 1520639858, 1520621858, 1),
(6, '19', 'mancore', '@mancore :yeah\n', 0, 1520639870, 1520621870, 1),
(7, '19', 'mancore', 'Just signedup', 0, 1520639881, 1520621881, 1),
(8, '22', 'demo123', 'Me too!!', 0, 1520640077, 1520622077, 1),
(9, '19', 'mancore', 'That was fast!', 0, 1520640086, 1520622086, 1),
(10, '19', 'mancore', 'Lets buy some EOS', 0, 1520641369, 1520623369, 1),
(11, '19', 'mancore', 'yeah', 0, 1520641380, 1520623380, 1),
(12, '22', 'demo123', 'see u later\n', 0, 1520641389, 1520623389, 1),
(13, '19', 'mancore', 'OKKKK\n', 0, 1520641395, 1520623395, 1),
(14, '22', 'demo123', 'Sell your LTC !!', 0, 1520641400, 1520623400, 1),
(15, '19', 'mancore', 'fine\n', 0, 1520641413, 1520623413, 1),
(16, '22', 'demo123', 'BCH to the moon!!!', 0, 1520641420, 1520623420, 1),
(17, '19', 'mancore', 'fine\n', 0, 1520641413, 1520623413, 1),
(18, '22', 'demo123', 'Buy XRP!!', 0, 1520641420, 1520623420, 1),
(19, '19', 'mancore', 'fine\n', 0, 1520641413, 1520623413, 1),
(59, '19', 'mancore', 'fine\n', 0, 1520641413, 1520623413, 1),
(139, '19', 'mancore', '@demo123 :stop spamm', 0, 1520641637, 1520623637, 1),
(140, '22', 'demo123', 'ok\n', 0, 1520642838, 1520624838, 1),
(141, '22', 'demo123', 'yeah\n', 0, 1520642855, 1520624855, 1),
(142, '22', 'demo123', 'HMm\n', 0, 1520642865, 1520624865, 1),
(143, '23', 'demouser', 'Bravo', 0, 1521074844, 1521056844, 1),
(144, '23', 'demouser', 'kk\n', 0, 1521555878, 1521537878, 1),
(145, '23', 'demouser', 'm\n', 0, 1521656571, 1521638571, 1),
(146, '30', '123456789', 'jjee\n', 0, 1524273861, 1524255861, 1),
(147, '30', '123456789', '@demouser :True\n', 0, 1524273885, 1524255885, 1),
(148, '1', 'technicator', 'Chat looks good', 0, 1524674831, 1524656831, 1),
(149, '1', 'technicator', 'Ltc to usd?\n', 0, 1524675146, 1524657146, 1),
(150, '1', 'technicator', 'Just got my 2fa', 0, 1524675276, 1524657276, 1),
(151, '1', 'technicator', 'Amazing', 0, 1525351803, 1525333803, 1),
(152, '31', '3216549870', 'Looks nice\n', 0, 1525452500, 1525434500, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_coin`
--

DROP TABLE IF EXISTS `codono_coin`;
CREATE TABLE IF NOT EXISTS `codono_coin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'rmb=usd, qbb=bitcoin, eth=eth based,rgb=ico',
  `title` varchar(50) DEFAULT NULL,
  `img` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `fee_bili` varchar(50) DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL COMMENT '',
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) UNSIGNED DEFAULT NULL,
  `block` varchar(250) DEFAULT NULL COMMENT 'eth block',
  `fee_meitian` varchar(200) NOT NULL COMMENT 'Daily limit',
  `dj_zj` varchar(200) DEFAULT NULL,
  `dj_dk` varchar(200) DEFAULT NULL,
  `dj_yh` varchar(200) DEFAULT NULL,
  `dj_mm` varchar(200) DEFAULT NULL,
  `zr_zs` varchar(50) DEFAULT NULL,
  `zr_jz` varchar(50) DEFAULT NULL,
  `zr_dz` varchar(50) DEFAULT NULL,
  `zr_sm` varchar(50) DEFAULT NULL,
  `zc_sm` varchar(50) DEFAULT NULL,
  `zc_fee` varchar(50) DEFAULT NULL,
  `zc_user` varchar(50) DEFAULT NULL,
  `zc_min` varchar(50) DEFAULT NULL,
  `zc_max` varchar(50) DEFAULT NULL,
  `zc_jz` varchar(50) DEFAULT NULL,
  `zc_zd` varchar(50) DEFAULT NULL,
  `js_yw` varchar(50) DEFAULT NULL,
  `js_sm` text,
  `js_qb` varchar(60) DEFAULT NULL COMMENT 'domain name',
  `js_ym` varchar(50) DEFAULT NULL,
  `js_gw` varchar(50) DEFAULT NULL,
  `js_lt` varchar(50) DEFAULT NULL,
  `js_wk` varchar(50) DEFAULT NULL,
  `cs_yf` varchar(50) DEFAULT NULL,
  `cs_sf` varchar(50) DEFAULT NULL,
  `cs_fb` varchar(50) DEFAULT NULL,
  `cs_qk` varchar(50) DEFAULT NULL,
  `cs_zl` varchar(50) DEFAULT NULL,
  `cs_cl` varchar(50) DEFAULT NULL,
  `cs_zm` varchar(50) DEFAULT NULL,
  `cs_nd` varchar(50) DEFAULT NULL,
  `cs_jl` varchar(50) DEFAULT NULL,
  `cs_ts` varchar(50) DEFAULT NULL,
  `cs_bz` varchar(50) DEFAULT NULL,
  `tp_zs` varchar(50) DEFAULT NULL,
  `tp_js` varchar(50) DEFAULT NULL,
  `tp_yy` varchar(50) DEFAULT NULL,
  `tp_qj` varchar(50) DEFAULT NULL,
  `codono_coinaddress` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='The currency allocation table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_coin`
--

INSERT INTO `codono_coin` (`id`, `name`, `type`, `title`, `img`, `sort`, `fee_bili`, `endtime`, `addtime`, `status`, `block`, `fee_meitian`, `dj_zj`, `dj_dk`, `dj_yh`, `dj_mm`, `zr_zs`, `zr_jz`, `zr_dz`, `zr_sm`, `zc_sm`, `zc_fee`, `zc_user`, `zc_min`, `zc_max`, `zc_jz`, `zc_zd`, `js_yw`, `js_sm`, `js_qb`, `js_ym`, `js_gw`, `js_lt`, `js_wk`, `cs_yf`, `cs_sf`, `cs_fb`, `cs_qk`, `cs_zl`, `cs_cl`, `cs_zm`, `cs_nd`, `cs_jl`, `cs_ts`, `cs_bz`, `tp_zs`, `tp_js`, `tp_yy`, `tp_qj`, `codono_coinaddress`) VALUES
(7, 'eth', 'eth', 'ETH', '59845aa8b444f.png', 0, '0', 0, 0, 1, '7860461', '', '3.82.172.25', '8545', '', 'OGNvSWpPTmdxMWJXQUJFTllzVHRmUT09', '0', '1', '1', '', '', '0', '', '0.01', '10000', '1', '10', 'Ethereum', '<span style=\"color:#333333;font-family:sans-serif;font-size:14px;line-height:24px;background-color:#FFFFFF;\">Ethernet is the next generation cryptography books, intended to support a number of advanced features, including user issuing currency trading, intelligent protocol, decentralized and a completely decentralized autonomous organization we think of(DAOs)Or decentralized autonomous company (DACs)application. Ethernet Square (Ethereum) Is not the type of each individual characteristic features as special support, opposite, Square ether (Ethereum) Includes a built-Turing-complete scripting language that allows through a mechanism called contract to want to achieve for their properties to write code. A contract is like an automatic proxy, whenever a transaction is received, the contract will run a specific piece of code, the code can be modified internal contract data stored or transmitted transaction. Senior contract can even modify its code. Place pre-sale received by Ethernet31531Bits coins, sold more than6000Wan Ethernet.ethcoin</span>', 'https://www.ethereum.org/cli', 'https://github.com/ethereum/', 'https://www.ethereum.org/', 'https://forum.ethereum.org/', 'https://www.ethereum.org/', 'Vitalik Buterin', 'Ethash', '2015/11/1', '15A second block', '76623310', '', '', '', '5.9', 'With features and intelligent digital currency ', 'None', '', '', '', '', ''),
(1, 'usd', 'rmb', 'USD', 'usd.png', 0, '', 0, 0, 1, '3276170', '', '182.254.134.191', '0', '0', '0', '0', '1', '0', '0', '0', '', '', '', '', '1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(8, 'etc', 'rgb', 'ETC', '59845cab4ba05.png', 0, '10', 0, 0, 1, '3276170', '20', '', '', '', '', '0', '1', '1', '', '', '0', '', '0', '10000', '1', '10', 'Ethereum Classic', '<span style=\"color:#333333;font-family:sans-serif;font-size:14px;line-height:24px;background-color:#FFFFFF;\">Ethernet classic (Ethereum Classic) Ethernet is the result of the project for the SquareDAOOr not to comply with financial problems after upgrading a hard bifurcation Ethernet Square branch block, retaining the original code rules and features Ethernet Square. After a hard bifurcation Ethernet Square block chain, there was the original Ethernet network with the new network SquareEthereum ClassicCoexistence situation. Block chain group adhere to the core values   of the developer compositionEthereum Classic, Issued a separate encryption currencyETC. As a complete currency, attracted a group of people before missed investment opportunities; and parallels with the original Ethernet Square, is also a way of Ethernet Square model stick.</span>', 'https://ethereumclassic.github.io/', 'https://github.com/ethereumclassic', 'https://ethereumclassic.github.io/', 'https://www.reddit.com/r/EthereumClassic', 'https://ethereumclassic.github.io/', '', '', '2015/11/1', '60second/Piece', '76623310', '', '', '', '', 'Separate encryption currency', '', '', '', '', '', ''),
(6, 'btc', 'qbb', 'Bitcoin', '598458a23a26a.png', 0, '0', 0, 0, 1, '3276170', '', '47.52.132.176:6678', '6678', 'omnicoreadmin', 'afafrgkgit784i5tg', '0', '1', '1', '', '', '0.01', '', '0.01', '10000', '1', '10', 'Bitcoin', 'SHA-256 is a cryptographic hash function that takes an input of a random size and produces an output of a fixed size. Hash functions are powerful because they are ‘one-way’. What this is means is, it is possible for anyone to use a hash function to produce an output when given an input. However, it is impossible to use the output of the hash function to reconstruct its given input. This powerful feature of the SHA-256 hash function makes it ideal for application within the Bitcoin network.', 'https://bitcoin.org/en/download', 'https://github.com/bitcoin/bitcoin', 'https://bitcoin.org/en/', 'http://bitcointalk.org/', 'https://www.blockchain.com/btc/tx/', 'Satoshi Nakamoto', 'SHA-256', '2009/01/09', '600second/Piece', '210000000', '', '', '', '25 BTC', 'Virtual currency originators, the widest audience,', 'Confirm long time', '', '', '', '', ''),
(9, 'ltc', 'coinpay', 'Litecoin', '59845c9fb46c2.png', 0, '0', 0, 0, 1, '3276170', '', 'XXXXXXXX', 'XXXXXXXX', 'XXXXXXXX', 'XXXXXXXX', '0', '1', '1', '', '', '0', '', '0.01', '10000', '1', '10', 'Litecoin Live', '<br />', 'https://litecoin.org/', 'https://github.com/litecoin-project', 'http://explorer.litecoin.net/', 'https://litecoin.org/', 'https://litecointalk.org/', 'Charls Lee', 'Scrypt', '2011/10/7', '150 second/Piece', '84000000', '35123000', 'PoW', '2016 Blocks', '50 LTC', 'High popularity', 'Promotion stalled', '', '', '', '', ''),
(10, 'bcc', 'qbb', 'BCH', '59845cb49d7d8.png', 0, '10', 0, 0, 1, '3276170', '20', '10.10.10.10', '1223', '123456789', '123456789', '0', '1', '1', '', '', '0', '', '0', '10000', '1', '10', 'Bitcoin Cash', 'In May 2017, bitcoin transactions took up to four days to complete.[10] In order to speed transactions, users could pay a transaction fee, which at the end of 2017 averaged about $28.[10] The delay and especially the fees made bitcoin impractical for everyday use to make small purchases.[11]<br />\r\n<br />\r\nIdea forms<br />\r\nUp until July 2017, bitcoin users maintained a common set of rules for the cryptocurrency.[12] On July 20, 2017 at block height 476768 Bitcoin Improvement Proposal (BIP) 91 was locked in (i.e. scheduled to activate at block height 477120).[13][14] It was designed to reject blocks created by miners not supporting Segregated Witness (SegWit).[13][14]<br />\r\n<br />\r\nSome members of the bitcoin community felt that adopting BIP 91 without increasing the block-size limit favored people who wanted to treat bitcoin as a digital investment rather than as a transactional currency[12][15] and devised a plan to increase the number of transactions its ledger can process by increasing the block size limit to eight megabytes.[16][17] CoinDesk said that these motivations might have been behind the development and launch of Bitcoin Cash:[18]<br />\r\n<br />\r\nSome users wanted an increase in bitcoin\'s block size limit parameter<br />\r\nSegWit was likely to activate and some users wanted to avoid the feature<br />\r\nThe likelihood that SegWit2x would not launch in 2017<br />\r\nThe Bitcoin Cash name was originally proposed by Chinese mining pool ViaBTC.[19][20]<br />\r\n<br />\r\nDevelopment<br />\r\nThe first implementation of the Bitcoin Cash protocol called Bitcoin ABC was revealed by Amaury \"Deadal Nix\" Séchet at the Future of Bitcoin conference in Arnhem, Netherlands.[19] Subsequently, Bitcoin Unlimited made its first release of Bitcoin Cash compatible software, named BUCash[21] and Bitcoin XT also released before the Bitcoin Cash fork.[22] This meant that 3 full node clients were available before the Bitcoin Cash hard fork on August 1, 2017.<br />\r\n<br />\r\nThese clients implemented the following changes from bitcoin:<br />\r\n<br />\r\nIncrease maximum block size to 8 megabytes.<br />\r\nModification of the transaction signature hashing algorithm. This provides replay protection for Bitcoin Cash transactions against the original bitcoin chain.<br />\r\nReplaced the original bitcoin difficulty algorithm (a difficulty adjustment every 2016 blocks), with the new Emergency Difficulty Adjustment (EDA) algorithm. EDA was intended to allow difficulty to more quickly respond to drastic drops in hashrate presumed to occur immediately post fork.<br />\r\nLaunch<br />\r\nUpon launch, Bitcoin Cash inherited the transaction history of the bitcoin cryptocurrency on that date, but all later transactions were separate. Block 478558 was the last common block and thus the first separate Bitcoin Cash block was 478559.[23] Bitcoin Cash cryptocurrency wallets started to reject bitcoin blocks and bitcoin transactions after 13:20 UTC, August 1, 2017 because it used a timer to initiate a fork. One exchange started Bitcoin Cash futures trading at 0.5 BTC on July 23; the futures dropped to 0.1 BTC by July 30. Market cap appeared since 23:15 UTC, August 1, 2017.[15][24] Per the coinmarketcap.com site, the price of BTC on August 1, 2017 was USD$2,718.26[25] and the price of BCH was USD$380.01,[26] which suggests the BCH split ratio to be 0.12265. The launch of Bitcoin Cash has created an ideological divide over which chain is the true bitcoin.[27][28]<br />', 'https://www.bitcoincash.org/', 'https://github.com/Bitcoin-ABC/bitcoin-abc', 'https://www.bitcoincash.org/', 'https://www.bitcoincash.org/', 'https://www.bitcoincash.org/', '', 'SHA-256', '2017/08/01', '', '210000000', '', '', '', '', '', '', '', '', '', '', ''),
(12, 'ast', 'rgb', 'Oersted', '5984689c5832f.png', 0, '10', 0, 0, 1, '3276170', '20', '', '', '', '', '0', '1', '1', '', '', '0', '', '0', '10000', '1', '10', 'anastoms', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(13, 'mtc', 'rgb', 'MTC', '5984695b52ce1.png', 0, '0', 0, 0, 1, '3276170', '', '', '', '123456789', '123456789', '0', '1', '1', '', '', '0', '', '0', '10000', '0', '10', 'MTC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(14, 'eos', 'rgb', 'EOS', '5984699448d41.png', 0, '0', 0, 0, 1, '3276170', '', '', '', '13059840358', '13059840358', '0', '1', '0', '', '', '0', '', '0', '10000', '1', '10', 'EOS', 'https://eos.io/', 'https://eos.io/', 'https://github.com/eosio', 'https://eos.io/', 'https://eos.io/chat', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(16, 'ico', 'rgb', 'ICC', '59846a22e55ae.png', 0, '0', 0, 0, 1, '3276170', '', '', '', '13059840358', '13059840358', '0', '1', '1', '', '', '0', '', '0', '10000', '1', '10', 'ICC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(17, 'powr', 'rgb', 'Power', '5ab0f6dc6ddd0.jpg', NULL, '10', NULL, NULL, 1, '3276170', '20', '127.0.0.1', '12345', 'powr', 'powr', '0', '1', '1', NULL, NULL, '0', NULL, '0', '10000', '1', '10', 'powr', '<p style=\"color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	Power Ledger is the world leading peer-to-peer marketplace for renewable energy.\r\n</p>\r\n<p style=\"color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	The Power Ledger Token (POWR) is the fuel of the Power Ledger Ecosystem<br />\r\nwith bespoke private trading applications creating Sparkz in exchange for<br />\r\nPOWRs.<br />\r\nThe Power Ledger Ecosystem supports a growing number of energy trading<br />\r\napplications. The key classes of Platform Applications developed by Power<br />\r\nLedger is listed below, with some already operational, and others in advanced<br />\r\nconceptual design or in development.\r\n</p>\r\n<p style=\"color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	1. P2P TRADING<br />\r\n2. NEO-RETAILER<br />\r\n3. MICROGRID/EMBEDDED NETWORK OPERATOR/STRATA<br />\r\n4. WHOLESALE MARKET SETTLEMENT<br />\r\n5. AUTONOMOUS ASSET (AA) MANAGEMENT<br />\r\n6. DISTRIBUTED MARKET MANAGEMENT<br />\r\n7. ELECTRIC VEHICLES<br />\r\n8. POWER PORT<br />\r\n9. CARBON TRADING<br />\r\n10. TRANSMISSION EXCHANGE\r\n</p>\r\n<iframe width=\"640\" height=\"360\" src=\"https://www.youtube.com/embed/7U0YojaoigI?feature=oembed\" frameborder=\"0\">\r\n</iframe>', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(18, 'doge', 'qbb', 'Doge', '5bed7200e5fb0.png', NULL, '0', NULL, NULL, 1, '3276170', '', '0.0.0.0', '1234', 'user', 'password', '0', '1', '1', NULL, NULL, '0.01', '0', '0', '10000', '1', '10', 'doge', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, 'SOMEADDRESSWHEREFEESGOES'),
(19, 'tbtc', 'blockio', 'tbtc', '5bed715029fe0.png', NULL, '0', NULL, NULL, 1, '3276170', '', 'block.io', '0000', 'APIKEY', 'APIPIN', '0', '1', '1', NULL, NULL, '1', '0', '0.01', '10000', '1', '0.0001', 'tbtc', '', 'k', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, ''),
(33, 'xrp', 'rgb', 'Ripple', '', NULL, '0', NULL, NULL, 1, NULL, '', '', '', '', NULL, '0', '1', '1', NULL, NULL, '0', '0', '0.01', '10000', '1', '10', 'xrp', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, ''),
(23, 'waves', 'waves', 'waves', '5bf7c1ed8dd88.png', NULL, '0', NULL, NULL, 1, '1330665', '', 'XXX', '384', '', 'kjjj', '0', '1', '1', NULL, NULL, '0', '0', '0.002', '10000', '1', '10', 'waves', '            ', '', '', '', '', 'https://wavesexplorer.com/tx/', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, 'jjj'),
(24, 'ltct', 'coinpay', 'Litecoin', '5bfa8706d8eb7.png', NULL, '0', NULL, NULL, 1, '3276170', '', 'XXXXXXXX', 'XXXXXXXX', 'XXXXXXXX', 'XXXXXXXX', '0', '1', '1', NULL, NULL, '0', '0', '0', '10000', '1', '10', 'Litecoin Testnet', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `codono_coinmarketcap`
--

DROP TABLE IF EXISTS `codono_coinmarketcap`;
CREATE TABLE IF NOT EXISTS `codono_coinmarketcap` (
  `id` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `price_usd` varchar(50) DEFAULT NULL,
  `price_btc` varchar(50) DEFAULT NULL,
  `24h_volume_usd` varchar(50) DEFAULT NULL,
  `market_cap_usd` varchar(50) DEFAULT NULL,
  `available_supply` varchar(50) DEFAULT NULL,
  `total_supply` varchar(50) DEFAULT NULL,
  `max_supply` varchar(50) DEFAULT NULL,
  `percent_change_1h` varchar(50) DEFAULT NULL,
  `percent_change_24h` varchar(50) DEFAULT NULL,
  `percent_change_7d` varchar(50) DEFAULT NULL,
  `last_updated` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_coinpay_ipn`
--

DROP TABLE IF EXISTS `codono_coinpay_ipn`;
CREATE TABLE IF NOT EXISTS `codono_coinpay_ipn` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'index',
  `funded` varchar(1) DEFAULT '0' COMMENT 'Check if fund has been added to user account',
  `address` varchar(100) DEFAULT NULL COMMENT 'user exchange address map',
  `dest_tag` varchar(80) DEFAULT NULL COMMENT 'XRP/XMR like ',
  `cid` varchar(100) DEFAULT NULL COMMENT 'coinpay id [withdrawal only]',
  `amount` varchar(50) DEFAULT NULL COMMENT 'coinpay fees',
  `amounti` varchar(50) DEFAULT NULL COMMENT 'coinpay fees',
  `confirms` int(11) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL COMMENT 'COIN NAME in Caps',
  `deposit_id` varchar(50) DEFAULT NULL,
  `fee` varchar(50) DEFAULT NULL COMMENT 'coinpay fees',
  `feei` varchar(50) DEFAULT NULL,
  `fiat_amount` varchar(50) DEFAULT '0.00000000',
  `fiat_amounti` varchar(50) DEFAULT '0',
  `fiat_coin` varchar(3) DEFAULT NULL,
  `fiat_fee` varchar(50) DEFAULT NULL,
  `fiat_feei` varchar(20) DEFAULT NULL,
  `ipn_id` varchar(50) DEFAULT NULL,
  `ipn_mode` varchar(25) DEFAULT NULL,
  `ipn_type` varchar(20) DEFAULT NULL,
  `ipn_version` varchar(50) DEFAULT NULL,
  `merchant` varchar(32) DEFAULT NULL COMMENT 'dont expose it to users',
  `status` int(11) DEFAULT NULL,
  `status_text` varchar(50) DEFAULT NULL,
  `txn_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_coin_comment`
--

DROP TABLE IF EXISTS `codono_coin_comment`;
CREATE TABLE IF NOT EXISTS `codono_coin_comment` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `cjz` int(11) UNSIGNED DEFAULT NULL,
  `tzy` int(11) UNSIGNED DEFAULT NULL,
  `xcd` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(10) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_coin_json`
--

DROP TABLE IF EXISTS `codono_coin_json`;
CREATE TABLE IF NOT EXISTS `codono_coin_json` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=570 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `codono_config`
--

DROP TABLE IF EXISTS `codono_config`;
CREATE TABLE IF NOT EXISTS `codono_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `footer_logo` varchar(200) NOT NULL COMMENT ' ',
  `topup_zidong` varchar(200) NOT NULL COMMENT 'name',
  `sidekick` varchar(200) DEFAULT NULL,
  `topup_openid` varchar(200) NOT NULL COMMENT 'name',
  `topup_appkey` varchar(200) NOT NULL COMMENT 'name',
  `index_lejimum` varchar(200) NOT NULL COMMENT 'Set up',
  `login_verify` varchar(200) NOT NULL COMMENT 'Set up',
  `fee_meitian` varchar(200) NOT NULL COMMENT 'Set up',
  `top_name` varchar(200) NOT NULL COMMENT 'Set up',
  `web_name` varchar(200) DEFAULT NULL,
  `web_title` varchar(200) DEFAULT NULL,
  `web_logo` varchar(200) DEFAULT NULL,
  `web_llogo_small` varchar(200) DEFAULT NULL,
  `web_keywords` text,
  `web_description` text,
  `web_close` text,
  `web_close_cause` text,
  `web_icp` text,
  `web_html_footer` text,
  `web_ren` text,
  `web_reg` text,
  `market_mr` text,
  `xnb_mr` text,
  `rmb_mr` text,
  `web_waring` text,
  `issue_warning` text,
  `cellphone_type` text,
  `cellphone_url` text,
  `cellphone_user` text,
  `cellphone_pwd` text,
  `contact_cellphone` text,
  `contact_twitter` text,
  `contact_facebook` text,
  `contact_qq` text,
  `contact_qqun` text,
  `contact_telegram` text,
  `contact_telegram_img` text,
  `contact_app_img` text,
  `contact_email` text,
  `contact_alipay` text,
  `contact_alipay_img` text,
  `contact_bank` text,
  `user_truename` text,
  `user_cellphone` text,
  `user_alipay` text,
  `user_bank` text,
  `user_text_truename` text,
  `user_text_cellphone` text,
  `user_text_alipay` text,
  `user_text_bank` text,
  `user_text_log` text,
  `user_text_password` text,
  `user_text_paypassword` text,
  `mytx_min` text,
  `mytx_max` text,
  `mytx_bei` text,
  `mytx_coin` text,
  `mytx_fee` text,
  `trade_min` text,
  `trade_max` text,
  `trade_limit` text,
  `trade_text_log` text,
  `issue_ci` text,
  `issue_jian` text,
  `issue_min` text,
  `issue_max` text,
  `money_min` text,
  `money_max` text,
  `money_bei` text,
  `money_text_index` text,
  `money_text_log` text,
  `money_text_type` text,
  `invit_type` text,
  `invit_fee1` text,
  `invit_fee2` text,
  `invit_fee3` text,
  `invit_text_txt` text,
  `invit_text_log` text,
  `index_notice_1` text,
  `index_notice_11` text,
  `index_notice_2` text,
  `index_notice_22` text,
  `index_notice_3` text,
  `index_notice_33` text,
  `index_notice_4` text,
  `index_notice_44` text,
  `text_footer` text,
  `shop_text_index` text,
  `shop_text_log` text,
  `shop_text_addr` text,
  `shop_text_view` text,
  `topup_text_index` text,
  `topup_text_log` text,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `shop_coin` varchar(200) NOT NULL COMMENT 'Calculation',
  `shop_logo` varchar(200) NOT NULL COMMENT 'MallLOGO',
  `shop_login` varchar(200) NOT NULL COMMENT 'Do you want to log in',
  `index_html` varchar(50) DEFAULT NULL,
  `trade_hangqing` varchar(50) DEFAULT NULL,
  `trade_moshi` varchar(50) DEFAULT NULL,
  `reg_award` tinyint(1) DEFAULT '0',
  `reg_award_coin` varchar(50) DEFAULT NULL,
  `reg_award_num` decimal(20,8) DEFAULT NULL,
  `ref_award` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Enable Referrer rewards',
  `ref_award_coin` varchar(30) NOT NULL COMMENT 'Referrer rewards currency',
  `ref_award_num` decimal(20,8) NOT NULL COMMENT 'Referrer rewards Amount',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='System Configuration Table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_config`
--

INSERT INTO `codono_config` (`id`, `footer_logo`, `topup_zidong`, `sidekick`, `topup_openid`, `topup_appkey`, `index_lejimum`, `login_verify`, `fee_meitian`, `top_name`, `web_name`, `web_title`, `web_logo`, `web_llogo_small`, `web_keywords`, `web_description`, `web_close`, `web_close_cause`, `web_icp`, `web_html_footer`, `web_ren`, `web_reg`, `market_mr`, `xnb_mr`, `rmb_mr`, `web_waring`, `issue_warning`, `cellphone_type`, `cellphone_url`, `cellphone_user`, `cellphone_pwd`, `contact_cellphone`, `contact_twitter`, `contact_facebook`, `contact_qq`, `contact_qqun`, `contact_telegram`, `contact_telegram_img`, `contact_app_img`, `contact_email`, `contact_alipay`, `contact_alipay_img`, `contact_bank`, `user_truename`, `user_cellphone`, `user_alipay`, `user_bank`, `user_text_truename`, `user_text_cellphone`, `user_text_alipay`, `user_text_bank`, `user_text_log`, `user_text_password`, `user_text_paypassword`, `mytx_min`, `mytx_max`, `mytx_bei`, `mytx_coin`, `mytx_fee`, `trade_min`, `trade_max`, `trade_limit`, `trade_text_log`, `issue_ci`, `issue_jian`, `issue_min`, `issue_max`, `money_min`, `money_max`, `money_bei`, `money_text_index`, `money_text_log`, `money_text_type`, `invit_type`, `invit_fee1`, `invit_fee2`, `invit_fee3`, `invit_text_txt`, `invit_text_log`, `index_notice_1`, `index_notice_11`, `index_notice_2`, `index_notice_22`, `index_notice_3`, `index_notice_33`, `index_notice_4`, `index_notice_44`, `text_footer`, `shop_text_index`, `shop_text_log`, `shop_text_addr`, `shop_text_view`, `topup_text_index`, `topup_text_log`, `addtime`, `status`, `shop_coin`, `shop_logo`, `shop_login`, `index_html`, `trade_hangqing`, `trade_moshi`, `reg_award`, `reg_award_coin`, `reg_award_num`, `ref_award`, `ref_award_coin`, `ref_award_num`) VALUES
(1, '5aa2851d37e88.png', '1', 'c', '', '', '1', '0', '', 'Welcome to BTCCoin.org', 'your.exchange', 'Advanced cryptocurrency exchange to buy and sell Bitcoin, Ethereum, Litecoin, Monero, ZCash, DigitalNote,', '5cc44c3bca454.png', '5aa28518b0450.png', 'bitcoin, buy bitcoin, bitcoin exchange, low fees, trading terminal, trading api, btc to usd, btc to eur', 'Codono is advanced cryptocurrency exchange to buy and sell Bitcoin, Ethereum, Litecoin, Monero, ZCash, DigitalNote,', '1', 'We are currently upgrading our system. Please come back in some time.', 'Copyright 2019 <a href=\"https://exchange.your.exchange\">Codono Exchange Software</a>', '', '100', '<div style=\"text-align:center;\">\r\n	<div style=\"text-align:left;\">\r\n		Codono Terms of Service<br />\r\n<br />\r\n1. Terms<br />\r\n<br />\r\n  By accessing the website at http://your.exchange, you are agreeing to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.<br />\r\n<br />\r\n2. Use License<br />\r\n<br />\r\n  <br />\r\n    <br />\r\n      Permission is granted to temporarily download one copy of the materials (information or software) on Codono\'s website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:<br />\r\n<br />\r\n      <br />\r\n        modify or copy the materials;<br />\r\n        use the materials for any commercial purpose, or for any public display (commercial or non-commercial);<br />\r\n        attempt to decompile or reverse engineer any software contained on Codono\'s website;<br />\r\n        remove any copyright or other proprietary notations from the materials; or<br />\r\n        transfer the materials to another person or \"mirror\" the materials on any other server.<br />\r\n      <br />\r\n    <br />\r\n    This license shall automatically terminate if you violate any of these restrictions and may be terminated by Codono at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.<br />\r\n  <br />\r\n<br />\r\n3. Disclaimer<br />\r\n<br />\r\n  <br />\r\n    The materials on Codono\'s website are provided on an \'as is\' basis. Codono makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.<br />\r\n    Further, Codono does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.<br />\r\n  <br />\r\n<br />\r\n4. Limitations<br />\r\n<br />\r\n  In no event shall Codono or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Codono\'s website, even if Codono or a Codono authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.<br />\r\n<br />\r\n5. Accuracy of materials<br />\r\n<br />\r\n  The materials appearing on Codono website could include technical, typographical, or photographic errors. Codono does not warrant that any of the materials on its website are accurate, complete or current. Codono may make changes to the materials contained on its website at any time without notice. However Codono does not make any commitment to update the materials.<br />\r\n<br />\r\n6. Links<br />\r\n<br />\r\n  Codono has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Codono of the site. Use of any such linked website is at the user\'s own risk.<br />\r\n<br />\r\n7. Modifications<br />\r\n<br />\r\n  Codono may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.<br />\r\n<br />\r\n8. Governing Law<br />\r\n<br />\r\n  These terms and conditions are governed by and construed in accordance with the laws of British Virgin Islands and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.<br />\r\n	</div>\r\n	<div style=\"text-align:left;\">\r\n		<span style=\"color:#337FE5;\"></span> \r\n	</div>\r\n</div>\r\n<p>\r\n	<span style=\"font-size:10px;color:#E56600;\"></span> \r\n</p>', 'btc_usd', 'btc', 'usd', '<div class=\"modal-body\">\r\n	<p>\r\n		Risk Warning: You acknowledge and agree that you shall \r\naccess and use the Technology Platform at your own risk. The risk of \r\nloss in Trading crypto assets can be substantial. You should, therefore,\r\n carefully consider whether such Trading is appropriate for you in light\r\n of your circumstances and resources.\r\n	</p>\r\n</div>', 'The new ICO risk warning not to put more than financial capacity to bear risk, no investment assets do not understand the figures, do not listen to any network in the name of We recommend buying coins to invest in publicity, resolutely resist the pyramid, wire fraud and money laundering and other illegal arbitrage.', '1', 'SMS Platform', '8b3c13ea', 'CgtfExSXljvm9oxh', '001-213-513-895', 'https://twitter.com/codono', '', '123123123123', '123123123123', 'https://t.me/codono', '5a25579dbb418.png', '5a2557a174748.png', 'support@your.exchange', 'something@domain.com', '56f98e6d7245d.jpg', 'Bank of America|Business Name|0000 0000 0000 0000', '2', '2', '2', '2', '&lt;span&gt;&lt;span&gt;Hello Member,Be sure to fill in your real name and true identity card number.&lt;/span&gt;&lt;/span&gt;', '&lt;span&gt;Hello Member,Be sure to authenticate the phone with their mobile phone number,After receiving the authentication codes may be used to.&lt;/span&gt;', '&lt;span&gt;Hello Member,Be sure to fill in the correct Alipay &amp;nbsp;(The same as the real-name authentication name) real names and Alipay account,Late withdrawals only basis.&lt;/span&gt;', '&lt;span&gt;Hello Member,&lt;/span&gt;&lt;span&gt;&lt;span&gt;Be sure to fill out the card information correctly Withdraw the sole basis.&lt;/span&gt;&lt;span&gt;&lt;/span&gt;&lt;/span&gt;', '&lt;span&gt;Their past records and operations login and registration spot.&lt;/span&gt;', '&lt;span&gt;Hello Member,After change my password, do not forget.If you remember the old password,Please click--&lt;/span&gt;&lt;span style=&quot;color:#EE33EE;&quot;&gt;forget password&lt;/span&gt;', '&lt;span&gt;Hello Member,After modifying transaction password please do not forget.If you remember the old transaction password,Please click--&lt;/span&gt;&lt;span style=&quot;color:#EE33EE;&quot;&gt;forget password&lt;/span&gt;', '100', '50000', '100', 'usd', '1', '1', '10000000', '10', '&lt;span&gt;&lt;span&gt;After you record the commission to buy or sell a successful trading.&lt;/span&gt;&lt;/span&gt;', '5', '24', '1', '100000', '100', '100000', '100', 'Money Home', 'Financial records', 'Money type', '1', '5', '3', '2', 'BTCCoin.Org Crypto Trading Platform', '&lt;span&gt;&lt;span&gt;To see your friend promotion,Please click&lt;/span&gt;&lt;span style=&quot;color:#EE33EE;&quot;&gt;+&lt;/span&gt;&lt;span&gt;,Meanwhile correct guidance and the sale of real-name authentication friend,Earn promotion revenue and transaction fees.&lt;/span&gt;&lt;/span&gt;', 'System reliability', 'Bank-level user data encryption, authentication dynamic multi-level risk identification control, and ensure the security', 'System reliability', 'Account multi-layer encryption, distributed servers off-line storage, data backup immediate isolation to ensure safety', 'fast and convenient', 'Instant recharge, rapid withdrawal, ten thousand orders per second, high-performance transaction engine to ensure everything quickly and easily', 'Professional Services', 'Dedicated customer service staff and24Hour technical team is always escort your account security', '', '', '', '', '', '', '', 1467383018, 0, '', '/Upload/shop/5ab0d2f822e98.png', '0', 'b', '1', '0', 0, 'eos', '2.00000000', 0, 'tbtc', '1.33500000');

-- --------------------------------------------------------

--
-- Table structure for table `codono_dice`
--

DROP TABLE IF EXISTS `codono_dice`;
CREATE TABLE IF NOT EXISTS `codono_dice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call` enum('low','high') DEFAULT NULL,
  `number` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'bid',
  `userid` int(11) NOT NULL,
  `result` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=win,2=loose',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `coinname` varchar(30) DEFAULT NULL,
  `winamount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `addtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_dividend`
--

DROP TABLE IF EXISTS `codono_dividend`;
CREATE TABLE IF NOT EXISTS `codono_dividend` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `coinjian` varchar(50) DEFAULT NULL,
  `content` text COMMENT 'Airdrop description',
  `image` varchar(255) DEFAULT '/Upload/airdrop/default_airdrop.png' COMMENT 'airdrop promo image',
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0' COMMENT 'featured',
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0= processed, 1= to be processed',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'active/inactive',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_dividend_log`
--

DROP TABLE IF EXISTS `codono_dividend_log`;
CREATE TABLE IF NOT EXISTS `codono_dividend_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `coinjian` varchar(50) DEFAULT NULL,
  `fenzong` varchar(50) DEFAULT NULL,
  `fenchi` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '1=enable , 0 disable',
  `userid` int(11) UNSIGNED NOT NULL COMMENT 'userid',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_faucet`
--

DROP TABLE IF EXISTS `codono_faucet`;
CREATE TABLE IF NOT EXISTS `codono_faucet` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `buycoin` varchar(50) DEFAULT NULL,
  `num` bigint(20) UNSIGNED DEFAULT NULL,
  `deal` int(11) UNSIGNED DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `ulimit` varchar(11) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `tian` varchar(255) DEFAULT NULL,
  `ci` varchar(255) DEFAULT NULL,
  `jian` varchar(255) DEFAULT NULL,
  `min` varchar(255) DEFAULT NULL,
  `max` varchar(255) DEFAULT NULL,
  `content` text,
  `invit_coin` varchar(50) DEFAULT NULL,
  `invit_1` varchar(50) DEFAULT NULL,
  `invit_2` varchar(50) DEFAULT NULL,
  `invit_3` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `tuijian` tinyint(1) NOT NULL DEFAULT '2',
  `homepage` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Show on homepage',
  `paixu` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Faucet List';

-- --------------------------------------------------------

--
-- Table structure for table `codono_faucet_log`
--

DROP TABLE IF EXISTS `codono_faucet_log`;
CREATE TABLE IF NOT EXISTS `codono_faucet_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT 'faucetid',
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL DEFAULT '********' COMMENT 'masked username',
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `buycoin` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` int(20) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `ci` int(11) UNSIGNED DEFAULT NULL,
  `jian` varchar(255) DEFAULT NULL,
  `unlock` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='Faucet Usages';

-- --------------------------------------------------------

--
-- Table structure for table `codono_finance`
--

DROP TABLE IF EXISTS `codono_finance`;
CREATE TABLE IF NOT EXISTS `codono_finance` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `userid` int(11) UNSIGNED NOT NULL COMMENT 'userid',
  `coinname` varchar(50) NOT NULL COMMENT 'Currencies',
  `num_a` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Prior to normal',
  `num_b` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Before freezing',
  `num` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Before Total',
  `fee` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The number of operations',
  `type` varchar(50) NOT NULL COMMENT 'Action Type',
  `name` varchar(50) NOT NULL COMMENT 'Operation Name',
  `nameid` int(11) NOT NULL COMMENT 'Action Details',
  `remark` varchar(50) NOT NULL COMMENT 'Action Remark',
  `mum_a` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The remaining normal',
  `mum_b` decimal(20,8) UNSIGNED NOT NULL COMMENT 'The remaining freeze',
  `mum` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Total surplus',
  `move` varchar(50) NOT NULL COMMENT 'Additional',
  `addtime` int(11) UNSIGNED NOT NULL COMMENT 'add time',
  `status` tinyint(4) UNSIGNED NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `coinname` (`coinname`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=729 DEFAULT CHARSET=utf8 COMMENT='Financial records table';

--
-- Dumping data for table `codono_finance`
--

INSERT INTO `codono_finance` (`id`, `userid`, `coinname`, `num_a`, `num_b`, `num`, `fee`, `type`, `name`, `nameid`, `remark`, `mum_a`, `mum_b`, `mum`, `move`, `addtime`, `status`) VALUES
(718, 65, 'usd', '10.00000000', '0.00000000', '10.00000000', '0.00000000', '1', 'Subscription', 1, 'Subscription PurchasePremium', '10.00000000', '0.00000000', '10.00000000', '07148b113948adf2928e1a55524d3122', 1581420829, 1),
(719, 65, 'usd', '10.00000000', '0.00000000', '10.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription PurchasePremium', '10.00000000', '0.00000000', '10.00000000', '94eab966053e52a53de3e1ce470b36a2', 1581420967, 1),
(720, 65, 'usd', '10.00000000', '0.00000000', '10.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription PurchasePremium', '9.00000000', '0.00000000', '9.00000000', '1f28f5b8ce558a4516b190449c8a1d3f', 1581421110, 1),
(721, 65, 'usd', '9.00000000', '0.00000000', '9.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription PurchasePremium', '8.00000000', '0.00000000', '8.00000000', 'e273d26a2faa1826e45875e260b08644', 1581421831, 1),
(722, 65, 'usd', '8.00000000', '0.00000000', '8.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription PurchasePremium', '7.00000000', '0.00000000', '7.00000000', 'add569d714619d54bb1dcc26cf68e999', 1581421896, 1),
(723, 65, 'usd', '7.00000000', '0.00000000', '7.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription Purchase:Premium', '6.00000000', '0.00000000', '6.00000000', '089b14f4c3dad52ae4e926c1189e25eb', 1581422001, 1),
(724, 65, 'usd', '6.00000000', '0.00000000', '6.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription Purchase:Premium', '5.00000000', '0.00000000', '5.00000000', '7b924c71c5524baa65d1634939f370dc', 1581422216, 1),
(725, 65, 'usd', '5.00000000', '0.00000000', '5.00000000', '0.00000000', '2', 'Subscription', 2, 'Subscription Purchase:Premium Plus', '5.00000000', '0.00000000', '5.00000000', '14d80f3d388f6437fbd90063daf4d998', 1581423467, 1),
(726, 65, 'usd', '5.00000000', '0.00000000', '5.00000000', '0.00000000', '2', 'Subscription', 1, 'Subscription Purchase:Premium', '4.00000000', '0.00000000', '4.00000000', 'd6ad422250adb296c9d6241ffc32d589', 1581423775, 1),
(727, 65, 'usd', '40000.00000000', '0.00000000', '40000.00000000', '1736.00000000', '2', 'trade', 16161, 'Trading Center-Buying commission-marketbtc_usd', '38264.00000000', '1736.00000000', '40000.00000000', 'ec3b892a4cdbec866fad634da24c8346', 1582719797, 0),
(728, 65, 'usd', '38264.00000000', '1736.00000000', '40000.00000000', '1736.00000000', '1', 'trade', 16161, 'Transaction Reversal btc_usd', '40000.00000000', '0.00000000', '40000.00000000', 'd746fcf5b2b73dc85b6bba65e74b4d23', 1582726405, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_investbox_log`
--

DROP TABLE IF EXISTS `codono_investbox_log`;
CREATE TABLE IF NOT EXISTS `codono_investbox_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `boxid` int(10) NOT NULL DEFAULT '0' COMMENT 'investbox id',
  `docid` varchar(30) NOT NULL DEFAULT '0' COMMENT 'investment certificate number',
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `begintime` int(11) NOT NULL DEFAULT '0',
  `endtime` int(11) NOT NULL DEFAULT '0',
  `withdrawn` int(11) NOT NULL DEFAULT '0' COMMENT 'premature withdraw time',
  `maturity` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT 'amount on maturity',
  `credited` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `userid` int(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COMMENT='User investment id';

-- --------------------------------------------------------

--
-- Table structure for table `codono_invit`
--

DROP TABLE IF EXISTS `codono_invit`;
CREATE TABLE IF NOT EXISTS `codono_invit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `invit` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `invit` (`invit`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='Promotion reward table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_issue`
--

DROP TABLE IF EXISTS `codono_issue`;
CREATE TABLE IF NOT EXISTS `codono_issue` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `buycoin` varchar(50) DEFAULT NULL,
  `num` bigint(20) UNSIGNED DEFAULT NULL,
  `deal` int(11) UNSIGNED DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `limit` int(11) UNSIGNED DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `tian` varchar(255) DEFAULT NULL,
  `ci` varchar(255) DEFAULT NULL,
  `jian` varchar(255) DEFAULT NULL,
  `min` varchar(255) DEFAULT NULL,
  `max` varchar(255) DEFAULT NULL,
  `content` text,
  `invit_coin` varchar(50) DEFAULT NULL,
  `invit_1` varchar(50) DEFAULT NULL,
  `invit_2` varchar(50) DEFAULT NULL,
  `invit_3` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `tuijian` tinyint(1) NOT NULL DEFAULT '2',
  `homepage` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Show on homepage',
  `paixu` int(5) NOT NULL DEFAULT '0',
  `video` varchar(255) DEFAULT NULL,
  `convertcurrency` varchar(30) DEFAULT NULL COMMENT 'currency in which user pays',
  `icobench` varchar(250) DEFAULT NULL,
  `icomark` varchar(250) DEFAULT NULL,
  `trackico` varchar(250) DEFAULT NULL,
  `facebook` varchar(250) DEFAULT NULL,
  `twitter` varchar(250) DEFAULT NULL,
  `telegram` varchar(250) DEFAULT NULL,
  `ownerid` int(10) DEFAULT '0' COMMENT 'ICO owner userid',
  `commission` varchar(255) NOT NULL DEFAULT '0.0' COMMENT 'Site commission',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='ICO issuing table';

--
-- Dumping data for table `codono_issue`
--

INSERT INTO `codono_issue` (`id`, `name`, `coinname`, `buycoin`, `num`, `deal`, `price`, `limit`, `time`, `tian`, `ci`, `jian`, `min`, `max`, `content`, `invit_coin`, `invit_1`, `invit_2`, `invit_3`, `sort`, `addtime`, `endtime`, `status`, `image`, `tuijian`, `homepage`, `paixu`, `video`, `convertcurrency`, `icobench`, `icomark`, `trackico`, `facebook`, `twitter`, `telegram`, `ownerid`, `commission`) VALUES
(5, 'ANASTOMS', 'ast', 'usd', 21000000, 21000000, '0.20000000', 10000000, '2017-08-01 00:00:00', '365', '0', '0', '10', '10000000', '', 'usd', '3', '2', '1', 0, 1571304704, 0, 1, '59846ad70be5f.png', 2, 0, 2, '', 'usd', '', '', '', '', '', '', 0, '0.0'),
(8, 'Arcona', 'eth', 'usd', 12500000, 9001000, '1.00000000', 10000, '2018-05-09 00:00:00', '120', '0', '0', '10', '1000', '<div class=\"row\" style=\"margin-left:-15px;color:#333333;font-family:Arial, Verdana;font-size:14px;background-color:#FFFFFF;\">\r\n	<div class=\"col-md-12\">\r\n		<div class=\"areaBox\" style=\"border:1px solid #000000;padding:10px;font-size:16px;\">\r\n			Arcona is an Augmented Reality Ecosystem which is based on the usage of blockchain technology to merge both the virtual world with the real world. Irrespective of location, users around the globe can link up with each other in this world of virtual and augmented reality - this enables the execution of virtual projects from anywhere around the globe without the need for users to leave the comfort of their homes.\r\n		</div>\r\n		<div>\r\n			<br />\r\n		</div>\r\n	</div>\r\n</div>', 'usd', '5', '', '3', NULL, 1526217647, NULL, 1, '5af57bf59e408.png', 2, 1, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.0'),
(9, 'ICC', 'ico', 'usd', 36800000, 9200155, '1.00000000', 10000, '2019-05-28 11:57:55', '30', '0', '0', '10', '10000', '<div class=\"col-md-12\">\r\n		Giza Device is providing a hardware crypto wallet which enables you t generate and create passwords when you need it. Giza Device is very particularly about the security of your crypto assets as such its products are of state-of-the-art and top notch secure technological standards. It is also easy to use so that even when you forget your password, you are able to follow simple steps and perform tasks only known to you to recover your passwords and gain access to your digital assets.\r\n	</div>\r\n	<div>\r\n	\r\n</div>', 'usd', '10', '2', '1', NULL, 1559126559, NULL, 1, '5af58aa8e4138.png', 1, 1, 0, 'HkglJzuuAcg', 'eth', 'alphax', 'alphax', 'blabber', 'https://www.facebook.com/alphaxofficialsupport/', 'https://twitter.com/alphax_official', 'https://t.me/alphaxcommunity', 51, '10.5'),
(6, 'MTC', 'mtc', 'eth', 21000000, 10002000, '0.20000000', 1000, '2017-08-01 00:00:00', '365', '0', '0', '1000', '1000', '<h2 style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"\">\r\n	What is mitcoin?\r\n</h2>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	mitcoin is an experimental new digital currency that enables instant payments to anyone, anywhere in the world. mitcoin uses peer-to-peer technology to operate with no central authority: managing transactions and issuing money are carried out collectively by the network. mitcoin Core is the name of open source software which enables the use of this currency.\r\n</p>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	For more information, as well as an immediately useable, binary version of the mitcoin Core software, see <a href=\"https://www.mitcoin.org/en/download\">https://www.mitcoin.org/en/download</a>.\r\n</p>\r\n<h2 style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"\">\r\n	<a id=\"user-content-license\" class=\"anchor\" href=\"https://github.com/anders94/mitcoin#license\"></a>License\r\n</h2>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	mitcoin Core is released under the terms of the MIT license. See <a href=\"https://github.com/anders94/mitcoin/blob/master/COPYING\">COPYING</a> for more information or see <a href=\"http://opensource.org/licenses/MIT\">http://opensource.org/licenses/MIT</a>.\r\n</p>\r\n<h2 style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"\">\r\n	<a id=\"user-content-development-process\" class=\"anchor\" href=\"https://github.com/anders94/mitcoin#development-process\"></a>Development process\r\n</h2>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	Developers work in their own trees, then submit pull requests when they think their feature or bug fix is ready.\r\n</p>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	If it is a simple/trivial/non-controversial change, then one of the mitcoin development team members simply pulls it.\r\n</p>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	If it is a <em>more complicated or potentially controversial</em> change, then the patch submitter will be asked to start a discussion (if they haven\'t already) on the <a href=\"https://lists.linuxfoundation.org/mailman/listinfo/mitcoin-dev\">mailing list</a>\r\n</p>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	The patch will be accepted if there is broad consensus that it is a good thing. Developers should expect to rework and resubmit patches if the code doesn\'t match the project\'s coding conventions (see <a href=\"https://github.com/anders94/mitcoin/blob/master/doc/developer-notes.md\">doc/developer-notes.md</a>) or are controversial.\r\n</p>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	The master branch is regularly built and tested, but is not guaranteed to be completely stable. <a href=\"https://github.com/mitcoin/mitcoin/tags\">Tags</a> are created regularly to indicate new official, stable release versions of mitcoin.\r\n</p>\r\n<h2 style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"\">\r\n	<a id=\"user-content-testing\" class=\"anchor\" href=\"https://github.com/anders94/mitcoin#testing\"></a>Testing\r\n</h2>\r\n<p style=\"color:#24292E;font-family:-apple-system, BlinkMacSystemFont, \"font-size:16px;\">\r\n	Testing and code review is the bottleneck for development; we get more pull requests than we can review and test on short notice. Please be patient and help out by testing other people\'s pull requests, and remember this is a security-critical project where any mistake might cost people lots of money.\r\n</p>', 'usd', '', '', '', 0, 1526042426, 0, 1, '5ae81e86dc758.jpg', 2, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.0'),
(7, 'Power Ledger', 'powr', 'eth', 10000, 4600, '0.00100000', 0, '2018-03-20 17:39:52', '180', '0', '0', '100', '100000', '<div class=\"whitelist row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Whitelist\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		No\r\n	</div>\r\n</div>\r\n<div class=\"token-sale-hard-cap row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Token Sale Hard Cap\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		TBD\r\n	</div>\r\n</div>\r\n<div class=\"token-sale-soft-cap row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Token Sale Soft Cap\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		TBD\r\n	</div>\r\n</div>\r\n<div class=\"token-symbol row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Token Symbol\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		POWR\r\n	</div>\r\n</div>\r\n<div class=\"token-type row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Token Type\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		ERC20, Ethereum\r\n	</div>\r\n</div>\r\n<div class=\"token-distribution row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Token Distribution\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		70% Platform dev & operations 15 % General overhead costs 15% Marketing\r\n	</div>\r\n</div>\r\n<div class=\"initial_token-price row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Initial Token Price\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		1 POWR = 0.0838 USD\r\n	</div>\r\n</div>\r\n<div class=\"kyc row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		KYC\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		No\r\n	</div>\r\n</div>\r\n<div class=\"particpation-restrictions row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Participation Restrictions\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		USA & China\r\n	</div>\r\n</div>\r\n<div class=\"accepts row\" style=\"margin-left:-20px;color:#333333;font-family:lato, sans-serif;font-size:15px;background-color:#FFFFFF;\">\r\n	<div class=\"col-xs-12 col-md-3 dim\" style=\"color:#BDC3C7;\">\r\n		Accepts\r\n	</div>\r\n	<div class=\"col-xs-12 col-md-9\">\r\n		ETH, BTC, LTC\r\n	</div>\r\n</div>', 'powr', '5', '4', '3', NULL, 1526217618, NULL, 1, '5ab0f8d883978.jpg', 2, 1, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.0');

-- --------------------------------------------------------

--
-- Table structure for table `codono_issue_log`
--

DROP TABLE IF EXISTS `codono_issue_log`;
CREATE TABLE IF NOT EXISTS `codono_issue_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `buycoin` varchar(50) DEFAULT NULL,
  `convertcurrency` varchar(50) DEFAULT NULL COMMENT 'Coin in which payment was made',
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `convert_price` decimal(20,8) DEFAULT NULL COMMENT 'converted payment',
  `num` int(20) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `ci` int(11) UNSIGNED DEFAULT NULL,
  `jian` varchar(255) DEFAULT NULL,
  `unlock` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='ICO record form';

-- --------------------------------------------------------

--
-- Table structure for table `codono_issue_timeline`
--

DROP TABLE IF EXISTS `codono_issue_timeline`;
CREATE TABLE IF NOT EXISTS `codono_issue_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) DEFAULT NULL,
  `phase_time` varchar(255) DEFAULT NULL,
  `phase_name` varchar(255) DEFAULT NULL,
  `phase_desc` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_link`
--

DROP TABLE IF EXISTS `codono_link`;
CREATE TABLE IF NOT EXISTS `codono_link` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `mytx` varchar(200) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='Common Bank Address';

--
-- Dumping data for table `codono_link`
--

INSERT INTO `codono_link` (`id`, `name`, `title`, `url`, `img`, `mytx`, `remark`, `sort`, `addtime`, `endtime`, `status`) VALUES
(52, 'Trantow-Leffler', 'Codono', 'https://your.exchange', '5aa2850f2b368.png', NULL, NULL, 0, 1522762249, 1522762249, 1),
(51, 'Feest-Cruickshank', 'Feest-Cruickshank', 'https://your.exchange', '5aa2850f2b368.png', NULL, NULL, 0, 1522762059, 1522762059, 1),
(50, 'Ferry, Strosin and Reilly', 'Ferry, Strosin and Reilly', 'https://your.exchange', '5aa2850f2b368.png', NULL, NULL, 0, 1522762020, 1522762020, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_log`
--

DROP TABLE IF EXISTS `codono_log`;
CREATE TABLE IF NOT EXISTS `codono_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` int(20) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `unlock` int(11) UNSIGNED DEFAULT NULL,
  `ci` int(11) UNSIGNED DEFAULT NULL,
  `recycle` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `codono_log`
--

INSERT INTO `codono_log` (`id`, `userid`, `coinname`, `name`, `price`, `num`, `mum`, `unlock`, `ci`, `recycle`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, NULL, 'ok', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `codono_market`
--

DROP TABLE IF EXISTS `codono_market`;
CREATE TABLE IF NOT EXISTS `codono_market` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `round` varchar(255) DEFAULT NULL,
  `fee_buy` varchar(255) DEFAULT NULL,
  `fee_sell` varchar(255) DEFAULT NULL,
  `buy_min` varchar(255) DEFAULT NULL,
  `buy_max` varchar(255) DEFAULT NULL,
  `sell_min` varchar(255) DEFAULT NULL,
  `sell_max` varchar(255) DEFAULT NULL,
  `trade_min` varchar(255) DEFAULT NULL,
  `trade_max` varchar(255) DEFAULT NULL,
  `invit_buy` varchar(50) DEFAULT NULL,
  `invit_sell` varchar(50) DEFAULT NULL,
  `invit_1` varchar(50) DEFAULT NULL,
  `invit_2` varchar(50) DEFAULT NULL,
  `invit_3` varchar(50) DEFAULT NULL,
  `zhang` varchar(255) DEFAULT NULL,
  `die` varchar(255) DEFAULT NULL,
  `hou_price` varchar(255) DEFAULT NULL,
  `tendency` varchar(1000) DEFAULT NULL,
  `trade` int(11) UNSIGNED DEFAULT '0',
  `new_price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `buy_price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `sell_price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `min_price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `max_price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `volume` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `change` decimal(20,8) DEFAULT NULL,
  `api_min` decimal(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000',
  `api_max` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `begintrade` varchar(20) DEFAULT NULL,
  `endtrade` varchar(20) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT '0',
  `addtime` int(11) UNSIGNED DEFAULT '0',
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `jiaoyiqu` tinyint(1) DEFAULT NULL,
  `market_ico_price` float(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Issue price',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='Quotes configuration table';

--
-- Dumping data for table `codono_market`
--

INSERT INTO `codono_market` (`id`, `name`, `round`, `fee_buy`, `fee_sell`, `buy_min`, `buy_max`, `sell_min`, `sell_max`, `trade_min`, `trade_max`, `invit_buy`, `invit_sell`, `invit_1`, `invit_2`, `invit_3`, `zhang`, `die`, `hou_price`, `tendency`, `trade`, `new_price`, `buy_price`, `sell_price`, `min_price`, `max_price`, `volume`, `change`, `api_min`, `api_max`, `begintrade`, `endtrade`, `sort`, `addtime`, `endtime`, `status`, `jiaoyiqu`, `market_ico_price`) VALUES
(7, 'ltc_usd', '3', '0.1', '0.125', '0.01', '10000000', '0.01', '10000000', '100', '10000000', '1', '1', '3', '2', '1', '', '', '292.03000000', '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '288.90000000', '0.00000000', '288.90000000', '288.55000000', '288.90000000', '2.00000000', '-1.07000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, 0, 1, 0, 0.00),
(8, 'bcc_usd', '3', '0.01', '0.015', '0.01', '1000000000', '0.01', '1000000000', '100', '1000000000', '1', '1', '3', '2', '1', '', '', '2616.12000000', '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '2616.82000000', '2500.00000000', '2616.82000000', '2616.82000000', '2616.82000000', '0.04300000', '0.03000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 99, 0, 0, 1, 0, 0.00),
(6, 'etc_usd', '3', '0', '0', '0.01', '10000000000000', '0.01', '10000000000000', '100', '10000000000000', '1', '1', '3', '2', '1', '', '', '103.13000000', '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '103.13000000', '103.15000000', '103.81000000', '103.10000000', '103.13000000', '30.00000000', '0.00000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, 0, 1, 0, 0.00),
(5, 'eth_usd', '3', '0.125', '0.130', '0.01', '10000000000000', '0.000001', '10000000000000', '0.000001', '10000000000000', '0', '0', '3', '2', '1', '', '', '1532.62000000', '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '225.00000000', '0.00000000', '225.00000000', '225.00000000', '256.16500000', '57.92024000', '-85.32000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, 0, 1, 0, 0.00),
(4, 'btc_usd', '3', '20', '10', '0.01', '100000', '0.01', '100000', '100', '1000000', '1', '1', '3', '2', '1', '', '10', '19288.62000000', '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '9372.14000000', '9370.00000000', '9372.20000000', '9210.00000000', '9444.39000000', '283071049.19488000', '0.01000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 100, 0, 0, 1, 0, 0.00),
(13, 'btc_eth', '6', '0.9', '0.1', '0.01', '10000000000000', '0.01', '10000000000000', '0.01', '10000000000000', '0', '0', '3', '2', '1', '', '', NULL, '[[1536142604,0],[1536157004,0],[1536171404,0],[1536185804,0],[1536200204,0],[1536214604,0],[1536229004,0],[1536243404,0],[1536257804,0],[1536272204,0],[1536286604,0],[1536301004,0],[1536315404,0],[1536329804,0],[1536344204,0],[1536358604,0],[1536373004,0],[1536387404,0],[1536401804,0]]', 1, '0.00000000', '0.00000000', '0.00000000', '0.00000000', '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, NULL, 1, 2, 0.00),
(15, 'ltc_btc', '8', '0.01', '0.02', '0.0001', '10000000000000', '0.0001', '10000000000000', '0.001', '10000', '1', '1', '', '', '', '', '', NULL, NULL, 1, '0.00658800', '0.00658000', '0.00660000', '0.00632000', '0.00668100', '372.18725797', '0.52000000', '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, NULL, 1, 1, 0.00),
(16, 'doge_btc', '8', '0.15', '0.15', '0.00000001', '100000000', '0.00000001', '100000000', '0.00000001', '1000000000', '1', '1', '', '', '', '', '', '0.00000001', NULL, 1, '0.00000000', '0.00000000', '0.00000000', '0.00000000', '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', '00:00:00', '23:59:00', 0, 0, NULL, 1, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `codono_market_json`
--

DROP TABLE IF EXISTS `codono_market_json`;
CREATE TABLE IF NOT EXISTS `codono_market_json` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `codono_market_json`
--

INSERT INTO `codono_market_json` (`id`, `name`, `data`, `type`, `sort`, `addtime`, `endtime`, `status`) VALUES
(3, 'bxb_usd', '[\"1.00000000\",\"0.01000000\",\"0.00001000\",\"0.00001000\"]', '', 0, 1497369599, 0, 0),
(5, 'ltc_usd', '[\"30.00000000\",\"8760.60000000\",\"0.00000000\",\"0.00000000\"]', '', 0, 1501862399, 0, 0),
(11, 'eos_usd', '[\"20.00000000\",\"246.20000000\",\"0.00000000\",\"0.00000000\"]', '', 0, 1501862399, 0, 0),
(16, 'abc_usd', '[\"12.00000000\",\"1.20000000\",\"0.00120000\",\"0.00120000\"]', '', 0, 1498838399, 0, 0),
(17, 'eth_usd', '[\"20.00000000\",\"30652.30000000\",\"0.00000000\",\"0.00000000\"]', NULL, NULL, 1501862399, NULL, NULL),
(19, 'bcc_usd', '[\"30.00000000\",\"78483.30000000\",\"0.00000000\",\"0.00000000\"]', NULL, NULL, 1501862399, NULL, NULL),
(21, 'etc_usd', '[\"30.00000000\",\"3093.40000000\",\"0.00000000\",\"0.00000000\"]', NULL, NULL, 1501862399, NULL, NULL),
(26, 'doge_btc', '[\"13.00000000\",\"0.00000013\",\"0.00000000\",\"0.00000000\"]', NULL, NULL, 1537286399, NULL, NULL),
(32, 'btc_usd', '', NULL, NULL, 1502323199, NULL, NULL),
(31, 'btc_usd', '[\"11.00000000\",\"212174.72000000\",\"0.00000000\",\"0.00000000\"]', NULL, NULL, 1501891199, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `codono_menu`
--

DROP TABLE IF EXISTS `codono_menu`;
CREATE TABLE IF NOT EXISTS `codono_menu` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'FileID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT 'title',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Sub-headingsID',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Sort (effectively the same level)',
  `url` char(255) NOT NULL DEFAULT '' COMMENT 'link address',
  `hide` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Whether to hide',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT 'prompt',
  `group` varchar(50) DEFAULT '' COMMENT 'Packet',
  `is_dev` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Whether visible only developer mode',
  `ico_name` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=460 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_menu`
--

INSERT INTO `codono_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`, `ico_name`) VALUES
(1, 'Dashboard', 0, 1, 'Index/index', 0, '', '', 0, 'home'),
(2, 'Content', 0, 1, 'Article/index', 0, '', '', 0, 'list-alt'),
(3, 'User', 0, 1, 'User/index', 0, '', '', 0, 'user'),
(4, 'Finance', 0, 1, 'Finance/index', 0, '', '', 0, 'th-list'),
(5, 'Trade', 0, 1, 'Trade/index', 0, '', '', 0, 'stats'),
(6, 'ICO', 0, 1, 'Game/index', 0, '', '', 0, 'globe'),
(7, 'Config', 0, 1, 'Config/index', 0, '', '', 0, 'cog'),
(8, 'Others', 7, 1, 'Operate/index', 1, '', '', 0, 'share'),
(9, 'Tools', 0, 1, 'Tools/index', 0, '', '', 0, 'wrench'),
(11, 'Dashboard', 1, 1, 'Index/index', 0, '', 'System', 0, 'home'),
(12, 'Market', 1, 3, 'Index/market', 0, '', 'System', 0, 'home'),
(13, 'Articles', 2, 1, 'Article/index', 0, '', 'Content', 0, 'list-alt'),
(14, 'Edit Add', 13, 1, 'Article/edit', 1, '', 'Content', 0, 'home'),
(15, 'Modify status', 13, 100, 'Article/status', 1, '', 'Content', 0, 'home'),
(16, 'upload image', 13, 2, 'Article/images', 1, '', 'Content', 0, '0'),
(18, 'Edit', 17, 2, 'Adver/edit', 1, '', 'Content', 0, '0'),
(19, 'Modify', 17, 2, 'Adver/status', 1, '', 'Content', 0, '0'),
(21, 'Edit', 20, 3, 'Chat/edit', 1, '', 'Chat', 0, '0'),
(22, 'Modify', 20, 3, 'Chat/status', 1, '', 'Chat', 0, '0'),
(23, 'Text Tips', 2, 1, 'Text/index', 1, '', 'Tips', 0, 'exclamation-sign'),
(24, 'Edit', 23, 1, 'Text/edit', 1, '', 'Tips', 0, '0'),
(25, 'Modify', 23, 1, 'Text/status', 1, '', 'Tips', 0, '0'),
(26, 'Users', 3, 1, 'User/index', 0, '', 'user', 0, 'user'),
(32, 'Confirm Withdraw', 26, 8, 'User/myzc_qr', 1, '', 'User Management', 0, '0'),
(33, 'User Config', 3, 1, 'User/config', 1, '', 'Frontend user management', 0, 'cog'),
(34, 'Edit', 33, 2, 'User/index_edit', 1, '', 'User Management', 0, '0'),
(35, 'Status', 33, 2, 'User/index_status', 1, '', 'User Management', 0, '0'),
(37, 'Edit Coin', 26, 3, 'Usercoin/edit', 1, '', 'User Management', 0, '0'),
(39, 'New User Group', 38, 0, 'AuthManager/createGroup', 1, '', 'authority management', 0, '0'),
(40, 'Edit User Groups', 38, 0, 'AuthManager/editgroup', 1, '', 'authority management', 0, '0'),
(41, 'Update User Group', 38, 0, 'AuthManager/writeGroup', 1, '', 'authority management', 0, '0'),
(42, 'Change state', 38, 0, 'AuthManager/changeStatus', 1, '', 'authority management', 0, '0'),
(43, 'Access authorization', 38, 0, 'AuthManager/access', 1, '', 'authority management', 0, '0'),
(44, 'Classification Authorization', 38, 0, 'AuthManager/category', 1, '', 'authority management', 0, '0'),
(45, 'Members of the authorized', 38, 0, 'AuthManager/user', 1, '', 'authority management', 0, '0'),
(46, 'Members of the list of authorized', 38, 0, 'AuthManager/tree', 1, '', 'authority management', 0, '0'),
(47, 'user group', 38, 0, 'AuthManager/group', 1, '', 'authority management', 0, '0'),
(48, 'Added to the user group', 38, 0, 'AuthManager/addToGroup', 1, '', 'authority management', 0, '0'),
(49, 'User group removed', 38, 0, 'AuthManager/removeFromGroup', 1, '', 'authority management', 0, '0'),
(50, 'Classified added to the user group', 38, 0, 'AuthManager/addToCategory', 1, '', 'authority management', 0, '0'),
(51, 'Model added to the user group', 38, 0, 'AuthManager/addToModel', 1, '', 'authority management', 0, '0'),
(53, 'Configuration', 52, 1, 'Finance/config', 1, '', 'Finance Config', 0, '0'),
(55, 'Types of', 52, 1, 'Finance/type', 1, '', 'Finance Type', 0, '0'),
(56, 'Modify status', 52, 1, 'Finance/type_status', 1, '', 'Finance Status', 0, '0'),
(60, 'modify', 57, 3, 'Mycz/status', 1, '', 'Top management', 0, '0'),
(61, 'Modify status', 57, 3, 'Mycztype/status', 1, '', 'Top management', 0, '0'),
(64, 'Modify status', 62, 5, 'Mytx/status', 1, '', 'Withdraw management', 0, '0'),
(65, 'cancel', 62, 5, 'Mytx/excel', 1, '', 'Withdraw management', 0, '0'),
(66, 'Importingexcel', 9, 5, 'Mytx/exportExcel', 1, '', 'Withdraw management', 0, '0'),
(68, 'Trade', 5, 1, 'Trade/index', 0, '', 'Trade', 0, 'stats'),
(69, 'Logs', 5, 2, 'Trade/log', 0, '', 'Trade', 0, 'stats'),
(70, 'Modify status', 68, 0, 'Trade/status', 1, '', 'Trade', 0, '0'),
(71, 'Revoked pending', 68, 0, 'Trade/reject', 1, '', 'Trade', 0, '0'),
(74, 'Edit ICO', 72, 2, 'Issue/edit', 1, '', 'ICO Management', 0, '0'),
(75, 'Modify ICO', 72, 2, 'Issue/status', 1, '', 'ICO Management', 0, '0'),
(79, 'Basic', 7, 1, 'Config/index', 0, '', 'Config', 0, 'cog'),
(80, 'SMS', 7, 2, 'Config/cellphone', 0, '', 'Config', 0, 'cog'),
(81, 'Support', 7, 3, 'Config/contact', 0, '', 'Config', 0, 'cog'),
(82, 'Bank', 79, 4, 'Config/bank', 0, '', 'Profiles', 0, 'credit-card'),
(83, 'edit', 82, 4, 'Config/bank_edit', 1, '', 'Profiles', 0, '0'),
(85, 'edit', 84, 4, 'Coin/edit', 0, '', 'Profiles', 0, '0'),
(87, 'Modify status', 84, 4, 'Coin/status', 1, '', 'Profiles', 0, '0'),
(89, 'Editing Market', 88, 4, 'Market/edit', 0, '', 'Market', 0, '0'),
(91, 'Modify status', 88, 4, 'Config/market_add', 1, '', 'Market', 0, '0'),
(92, 'Captcha', 95, 7, 'Verify/code', 1, '', 'Profiles', 0, '0'),
(93, 'Phone code', 95, 7, 'Verify/mobile', 1, '', 'Profiles', 0, '0'),
(94, 'Mail Code', 95, 7, 'Verify/email', 0, '', 'Profiles', 0, '0'),
(95, 'Misc', 7, 6, 'Config/misc', 0, '', 'Config', 0, 'cog'),
(97, 'Promotion', 8, 2, 'Invit/config', 0, '', 'Promotion', 0, 'cog'),
(101, 'Other module calls', 9, 4, 'Tools/invoke', 1, '', 'other', 0, '0'),
(102, 'Table Optimization', 9, 4, 'Tools/optimize', 1, '', 'other', 0, '0'),
(103, 'Repair Tables', 9, 4, 'Tools/repair', 1, '', 'other', 0, '0'),
(104, 'Removing Backup Files', 9, 4, 'Tools/del', 1, '', 'other', 0, '0'),
(105, 'backup database', 9, 4, 'Tools/export', 1, '', 'other', 0, ''),
(106, 'Restore Database', 9, 4, 'Tools/import', 1, '', 'other', 0, '0'),
(107, 'Export Database', 9, 4, 'Tools/excel', 1, '', 'other', 0, '0'),
(108, 'Export Excel', 9, 4, 'Tools/exportExcel', 1, '', 'other', 0, '0'),
(109, 'ImportingExcel', 9, 4, 'Tools/importExecl', 1, '', 'other', 0, '0'),
(115, 'image', 111, 0, 'Shop/images', 0, '', 'Store', 0, '0'),
(116, 'Menu Manager', 7, 5, 'Menu/index', 0, '', 'Development Team', 0, 'list'),
(117, 'Sequence', 116, 5, 'Menu/sort', 0, '', 'Development Team', 0, '0'),
(118, 'Add to', 116, 5, 'Menu/add', 0, '', 'Development Team', 0, '0'),
(119, 'edit', 116, 5, 'Menu/edit', 0, '', 'Development Team', 0, '0'),
(120, 'delete', 116, 5, 'Menu/del', 0, '', 'Development Team', 0, '0'),
(121, 'Whether to hide', 116, 5, 'Menu/toogleHide', 0, '', 'Development Team', 0, '0'),
(122, 'Whether development', 116, 5, 'Menu/toogleDev', 0, '', 'Development Team', 0, '0'),
(123, 'Import File', 7, 5, 'Menu/importFile', 1, '', 'Development Team', 0, 'log-in'),
(124, 'Importing', 7, 5, 'Menu/import', 1, '', 'Development Team', 0, 'log-in'),
(127, 'User login', 3, 0, 'Login/index', 1, '', 'User Configuration', 0, '0'),
(128, 'User exits', 3, 0, 'Login/loginout', 1, '', 'User Configuration', 0, '0'),
(129, 'Change the administrator password', 3, 0, 'User/setpwd', 1, '', 'user', 0, 'home'),
(131, 'User Details', 3, 4, 'User/detail', 1, '', 'Frontend user management', 0, 'time'),
(132, 'User Details background', 3, 1, 'AdminUser/detail', 1, '', 'User management background', 0, 'th-list'),
(133, 'Background user status', 3, 1, 'AdminUser/status', 1, '', 'User management background', 0, 'th-list'),
(134, 'Add admin', 3, 1, 'AdminUser/add', 1, '', 'User management background', 0, 'th-list'),
(135, 'Users to edit the background', 3, 1, 'AdminUser/edit', 1, '', 'User management background', 0, 'th-list'),
(138, 'edit', 2, 1, 'Articletype/edit', 1, '', 'Content Management', 0, 'list-alt'),
(140, 'edit', 139, 2, 'Link/edit', 1, '', 'Content Management', 0, '0'),
(141, 'modify', 139, 2, 'Link/status', 1, '', 'Content Management', 0, '0'),
(155, 'Server queue', 9, 3, 'Tools/queue', 0, '', 'Tool', 0, 'wrench'),
(156, 'Check the wallet', 9, 3, 'Tools/wallet', 1, '', 'Tool', 0, 'wrench'),
(157, 'Coin stats', 1, 2, 'Index/coin', 0, '', 'system', 0, 'home'),
(163, 'Tips', 7, 5, 'Config/text', 0, '', 'Config', 0, 'cog'),
(220, 'Coin Reviews', 5, 4, 'Trade/comment', 0, '', 'Trade', 0, 'stats'),
(278, 'Categories', 2, 2, 'Article/type', 0, '', 'content', 0, 'list-alt'),
(279, 'Slider', 2, 3, 'Article/adver', 0, '', 'content', 0, 'list-alt'),
(280, 'Footer Links', 2, 4, 'Article/link', 0, '', 'content', 0, 'list-alt'),
(282, 'Signin Log', 3, 4, 'User/log', 0, '', 'user', 0, 'user'),
(283, 'Users wallet', 3, 5, 'User/wallet', 0, '', 'user', 0, 'user'),
(284, 'Withdraw Address', 3, 6, 'User/bank', 0, '', 'user', 0, 'user'),
(285, 'User Coins', 3, 7, 'User/coin', 0, '', 'user', 0, 'user'),
(286, 'Address', 3, 8, 'User/goods', 0, '', 'user', 0, 'user'),
(287, 'Chat', 5, 3, 'Trade/chat', 0, '', 'Trade', 0, 'stats'),
(288, 'Market', 5, 5, 'Trade/market', 0, '', 'Trade', 0, 'stats'),
(289, 'Invite', 5, 6, 'Trade/invit', 0, '', 'Trade', 0, 'stats'),
(290, 'Financial details', 4, 1, 'Finance/index', 0, '', 'Financial', 0, 'th-list'),
(291, 'Fiat Deposit', 4, 2, 'Finance/mycz', 0, '', 'Financial', 0, 'th-list'),
(292, 'Payment Gateways', 4, 3, 'Finance/myczType', 0, '', 'Financial', 0, 'th-list'),
(293, 'Fiat Withdrawal', 4, 4, 'Finance/mytx', 0, '', 'Financial', 0, 'th-list'),
(294, 'Fiat Config', 4, 5, 'Finance/mytxConfig', 0, '', 'Financial', 0, 'th-list'),
(295, 'Crypto Deposit', 4, 6, 'Finance/myzr', 0, '', 'Financial', 0, 'th-list'),
(296, 'Crypto Withdraw', 4, 7, 'Finance/myzc', 0, '', 'Financial', 0, 'th-list'),
(297, 'Modify status', 291, 100, 'Finance/myczStatus', 1, '', 'Financial', 0, 'home'),
(298, 'Confirm arrival', 291, 100, 'Finance/myczConfirm', 1, '', 'Financial', 0, 'home'),
(299, 'Edit Add', 292, 1, 'Finance/myczTypeEdit', 1, '', 'Financial', 0, 'home'),
(300, 'Modify status', 292, 2, 'Finance/myczTypeStatus', 1, '', 'Financial', 0, 'home'),
(301, 'upload image', 292, 2, 'Finance/myczTypeImage', 1, '', 'Financial', 0, 'home'),
(302, 'Modify status', 293, 2, 'Finance/mytxStatus', 1, '', 'Financial', 0, 'home'),
(303, 'Export selected', 293, 3, 'Finance/mytxExcel', 1, '', 'Financial', 0, 'home'),
(304, 'Processing', 293, 4, 'Finance/mytxChuli', 1, '', 'Financial', 0, 'home'),
(305, 'Undo withdrawals', 293, 5, 'Finance/mytxReject', 1, '', 'Financial', 0, 'home'),
(306, 'Confirm Withdraw', 293, 6, 'Finance/mytxConfirm', 1, '', 'Financial', 0, 'home'),
(307, 'Confirm turn out', 296, 6, 'Finance/myzcConfirm', 1, '', 'Financial', 0, 'home'),
(309, 'Clear the cache', 9, 1, 'Tools/index', 0, '', 'tool', 0, 'wrench'),
(310, 'backup database', 9, 2, 'Tools/dataExport', 1, '', 'tool', 0, 'wrench'),
(311, 'Restore Database', 9, 2, 'Tools/dataImport', 1, '', 'tool', 0, 'wrench'),
(312, 'Admins', 3, 2, 'User/admin', 0, '', 'user', 0, 'user'),
(313, 'Permissions', 3, 3, 'User/auth', 0, '', 'user', 0, 'user'),
(314, 'Edit Add', 26, 1, 'User/edit', 1, '', 'user', 0, 'home'),
(315, 'Modify status', 26, 1, 'User/status', 1, '', 'user', 0, 'home'),
(316, 'Edit Add', 312, 1, 'User/adminEdit', 1, '', 'user', 0, 'home'),
(317, 'Modify status', 312, 1, 'User/adminStatus', 1, '', 'user', 0, 'home'),
(318, 'Edit Add', 313, 1, 'User/authEdit', 1, '', 'user', 0, 'home'),
(319, 'Modify status', 313, 1, 'User/authStatus', 1, '', 'user', 0, 'home'),
(320, 'Permission to re-initialize', 313, 1, 'User/authStart', 1, '', 'user', 0, 'home'),
(321, 'Edit Add', 282, 1, 'User/logEdit', 1, '', 'user', 0, 'home'),
(322, 'Modify status', 282, 1, 'User/logStatus', 1, '', 'user', 0, 'home'),
(323, 'Edit Add', 283, 1, 'User/walletEdit', 1, '', 'user', 0, 'home'),
(324, 'Modify status', 283, 1, 'User/walletStatus', 1, '', 'user', 0, 'home'),
(325, 'Edit Add', 284, 1, 'User/bankEdit', 1, '', 'user', 0, 'home'),
(326, 'Modify status', 284, 1, 'User/bankStatus', 1, '', 'user', 0, 'home'),
(327, 'Edit Add', 285, 1, 'User/coinEdit', 1, '', 'user', 0, 'home'),
(328, 'Coin Log', 285, 1, 'User/coinLog', 1, '', 'user', 0, 'home'),
(329, 'Edit Add', 286, 1, 'User/goodsEdit', 1, '', 'user', 0, 'home'),
(330, 'Modify status', 286, 1, 'User/goodsStatus', 1, '', 'user', 0, 'home'),
(331, 'Edit Add', 278, 1, 'Article/typeEdit', 1, '', 'content', 0, 'home'),
(332, 'Modify status', 278, 100, 'Article/typeStatus', 1, '', 'content', 0, 'home'),
(333, 'Edit Add', 280, 1, 'Article/linkEdit', 1, '', 'content', 0, 'home'),
(334, 'Modify status', 280, 100, 'Article/linkStatus', 1, '', 'content', 0, 'home'),
(335, 'Edit Add', 279, 1, 'Article/adverEdit', 1, '', 'content', 0, 'home'),
(336, 'Modify status', 279, 100, 'Article/adverStatus', 1, '', 'content', 0, 'home'),
(337, 'Image Update', 279, 100, 'Article/adverImage', 1, '', 'content', 0, 'home'),
(377, 'Access authorization', 313, 1, 'User/authAccess', 1, '', 'user', 0, 'home'),
(378, 'Access unauthorized modification', 313, 1, 'User/authAccessUp', 1, '', 'user', 0, 'home'),
(379, 'Members of the authorized', 313, 1, 'User/authUser', 1, '', 'user', 0, 'home'),
(380, 'Members of the authorized increase', 313, 1, 'User/authUserAdd', 1, '', 'user', 0, 'home'),
(381, 'Members of the authorized lifted', 313, 1, 'User/authUserRemove', 1, '', 'user', 0, 'home'),
(382, 'Coin', 7, 4, 'Config/coin', 0, '', 'Config', 0, 'cog'),
(383, 'Promotion award', 8, 1, 'Operate/index', 0, '', '', 0, 'share'),
(384, 'APP Config', 8, 1, 'App/config', 0, '', 'APP', 0, 'time'),
(385, 'APP VIP', 8, 2, 'App/vip_config_list', 0, '', 'APP', 0, 'time'),
(386, 'WAP Banners', 8, 3, 'Admin/App/ads_list/block_id/1', 0, '', 'WAP Banners', 0, 'time'),
(387, 'APP Ads', 8, 4, 'App/ads_user', 0, '', 'APP management', 0, 'time'),
(388, 'Menu', 7, 7, 'Config/navigation', 0, '', 'Config', 0, 'cog'),
(425, 'Products', 6, 1, 'Shop/index', 0, '', 'Store', 0, 'globe'),
(426, 'Config', 6, 2, 'Shop/config', 0, '', 'Store', 0, 'globe'),
(427, 'Categories', 6, 3, 'Shop/type', 0, '', 'Store', 0, 'globe'),
(428, 'Payment method', 6, 4, 'Shop/coin', 0, '', 'Store', 0, 'globe'),
(429, 'Orders', 6, 5, 'Shop/log', 0, '', 'Store', 0, 'globe'),
(430, 'Shipping address', 6, 6, 'Shop/goods', 0, '', 'Store', 0, 'globe'),
(433, 'Airdrop', 6, 3, 'Dividend/index', 0, '', 'Airdrop', 0, 'plane'),
(434, 'Records', 6, 5, 'Dividend/log', 0, '', 'Airdrop', 0, 'th-list'),
(435, 'Recharge record', 6, 1, 'Topup/index', 1, '', 'Prepaid recharge', 0, 'globe'),
(436, 'Recharge Configuration', 6, 1, 'Topup/config', 1, '', 'Prepaid recharge', 0, 'globe'),
(437, 'Recharge amount', 6, 3, 'Topup/type', 1, '', 'Prepaid recharge', 0, 'globe'),
(438, 'Topup method', 6, 4, 'Topup/coin', 1, '', 'Prepaid recharge', 0, 'globe'),
(439, 'Voting Record', 6, 1, 'Vote/index', 0, '', 'New Coin', 0, 'globe'),
(440, 'Voting type', 6, 1, 'Vote/type', 0, '', 'New Coin', 0, 'globe'),
(441, 'Money Management', 6, 1, 'Money/index', 1, '', 'Money Management', 0, 'globe'),
(442, 'Money Log', 6, 2, 'Money/log', 1, '', 'Money Management', 0, 'globe'),
(443, 'Financial details', 6, 3, 'Money/fee', 1, '', 'Money Management', 0, 'globe'),
(448, 'ICO', 6, 1, 'Issue/index', 0, '', 'ICO Management', 0, 'globe'),
(449, 'Records', 6, 1, 'Issue/log', 0, '', 'ICO Management', 0, 'globe'),
(450, 'Award', 3, 1, 'User/award', 0, '', 'user', 0, 'user'),
(452, 'Faucet', 6, 1, 'Faucet/index', 0, '', 'Faucet', 0, 'tree-deciduous'),
(453, 'Logs', 6, 1, 'Faucet/log', 0, '', 'Faucet', 0, 'tree-deciduous'),
(455, 'Invest', 0, 1, 'Invest/index', 0, '', '', 0, '0'),
(457, 'Investbox', 455, 0, 'Invest/Index', 0, '', 'Invest', 0, '0'),
(458, 'Investments', 455, 0, 'Invest/investlist', 0, '', 'Invest', 0, '0'),
(459, 'DiceRolls', 455, 0, 'Invest/dicerolls', 0, '', 'Invest', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `codono_message`
--

DROP TABLE IF EXISTS `codono_message`;
CREATE TABLE IF NOT EXISTS `codono_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `addip` varchar(200) DEFAULT NULL,
  `addr` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(10) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_message_log`
--

DROP TABLE IF EXISTS `codono_message_log`;
CREATE TABLE IF NOT EXISTS `codono_message_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `addip` varchar(200) DEFAULT NULL,
  `addr` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(10) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_money`
--

DROP TABLE IF EXISTS `codono_money`;
CREATE TABLE IF NOT EXISTS `codono_money` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `num` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `deal` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `tian` int(11) UNSIGNED DEFAULT NULL,
  `fee` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Finance and investment table';

--
-- Dumping data for table `codono_money`
--

INSERT INTO `codono_money` (`id`, `name`, `coinname`, `num`, `deal`, `tian`, `fee`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, '1111', 'bxb', 1, 0, 11, 11, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_money_fee`
--

DROP TABLE IF EXISTS `codono_money_fee`;
CREATE TABLE IF NOT EXISTS `codono_money_fee` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `money_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `num` int(6) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_money_log`
--

DROP TABLE IF EXISTS `codono_money_log`;
CREATE TABLE IF NOT EXISTS `codono_money_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `num` int(11) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) UNSIGNED DEFAULT NULL,
  `feea` decimal(20,8) UNSIGNED DEFAULT NULL,
  `tian` int(11) UNSIGNED DEFAULT NULL,
  `tiana` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `money_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Financial record sheet';

-- --------------------------------------------------------

--
-- Table structure for table `codono_mycz`
--

DROP TABLE IF EXISTS `codono_mycz`;
CREATE TABLE IF NOT EXISTS `codono_mycz` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `num` float(11,2) UNSIGNED DEFAULT NULL,
  `mum` float(11,2) UNSIGNED DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `tradeno` varchar(50) DEFAULT NULL,
  `remark` varchar(250) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `ipn_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'ipn status ',
  `ipn_response` text COMMENT 'json response',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 COMMENT='Recharge record form';

--
-- Dumping data for table `codono_mycz`
--

INSERT INTO `codono_mycz` (`id`, `userid`, `num`, `mum`, `type`, `tradeno`, `remark`, `sort`, `addtime`, `endtime`, `status`, `ipn_status`, `ipn_response`) VALUES
(75, 65, 1.00, 1.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `codono_mycz_invit`
--

DROP TABLE IF EXISTS `codono_mycz_invit`;
CREATE TABLE IF NOT EXISTS `codono_mycz_invit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `userid` int(11) UNSIGNED NOT NULL COMMENT 'userid',
  `invitid` int(11) UNSIGNED NOT NULL COMMENT 'Recommendedid',
  `num` decimal(20,2) UNSIGNED NOT NULL COMMENT 'Operating Amount',
  `fee` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Credits',
  `coinname` varchar(50) NOT NULL COMMENT 'Currency gift',
  `mum` decimal(20,8) UNSIGNED NOT NULL COMMENT 'Amount arrival',
  `remark` varchar(250) NOT NULL COMMENT 'Remark',
  `sort` int(11) UNSIGNED NOT NULL COMMENT 'Sequence',
  `addtime` int(11) UNSIGNED NOT NULL COMMENT 'add time',
  `endtime` int(11) UNSIGNED NOT NULL COMMENT 'Edit time',
  `status` tinyint(4) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Prepaid gift';

-- --------------------------------------------------------

--
-- Table structure for table `codono_mycz_type`
--

DROP TABLE IF EXISTS `codono_mycz_type`;
CREATE TABLE IF NOT EXISTS `codono_mycz_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `max` varchar(200) NOT NULL COMMENT 'name',
  `min` varchar(200) NOT NULL COMMENT 'name',
  `kaihu` varchar(200) NOT NULL COMMENT 'name',
  `truename` varchar(200) NOT NULL COMMENT 'name',
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `img` varchar(50) DEFAULT NULL,
  `extra` varchar(50) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Recharge type';

--
-- Dumping data for table `codono_mycz_type`
--

INSERT INTO `codono_mycz_type` (`id`, `max`, `min`, `kaihu`, `truename`, `name`, `title`, `url`, `username`, `password`, `img`, `extra`, `remark`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, '100000', '50', 'Alipay business', 'Codono Inc', 'alipay', 'Alipay transfers', '', 'support@your.exchange', '', '595607f635afa.png', '', 'Alipay account needs to be set inside Contact', 0, 0, 0, 1),
(2, '1000', '1', 'Authorize.net', 'Codonocom', 'authorize', 'Authorize.net', '', 'CodonoCom', 'YourExchange', 'authorizenet.png', '', 'You need to set up accounts in the micro-channel c', 0, 0, 0, 1),
(3, '50000', '100', 'Bank of America', 'Codono Inc', 'bank', 'Online bank transfer', '', '4325657823456789', '31495965', '5acc83366c0e8.png', '', 'Information required in the format in which the nu', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_mytx`
--

DROP TABLE IF EXISTS `codono_mytx`;
CREATE TABLE IF NOT EXISTS `codono_mytx` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL COMMENT 'Payment info',
  `num` int(11) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,2) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,2) UNSIGNED DEFAULT NULL,
  `truename` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `bank` varchar(250) DEFAULT NULL,
  `bankprov` varchar(50) DEFAULT NULL,
  `bankcity` varchar(50) DEFAULT NULL,
  `bankaddr` varchar(50) DEFAULT NULL,
  `bankcard` varchar(200) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Withdraw record form';

-- --------------------------------------------------------

--
-- Table structure for table `codono_myzc`
--

DROP TABLE IF EXISTS `codono_myzc`;
CREATE TABLE IF NOT EXISTS `codono_myzc` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `dest_tag` varchar(200) DEFAULT NULL COMMENT 'xmr.xrp etc',
  `coinname` varchar(200) DEFAULT NULL,
  `txid` varchar(200) DEFAULT NULL,
  `hash` varchar(256) DEFAULT NULL COMMENT 'Saving eth hash',
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_myzc_fee`
--

DROP TABLE IF EXISTS `codono_myzc_fee`;
CREATE TABLE IF NOT EXISTS `codono_myzc_fee` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coinname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `txid` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fee` decimal(20,8) DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `codono_myzr`
--

DROP TABLE IF EXISTS `codono_myzr`;
CREATE TABLE IF NOT EXISTS `codono_myzr` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `coinname` varchar(200) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT '0' COMMENT 'eth,qbb,rgb, [Mainly for eth type]',
  `txid` varchar(200) DEFAULT NULL,
  `shifted_to_main` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'if eth type deposit shiifted to main account',
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=638 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_navigation`
--

DROP TABLE IF EXISTS `codono_navigation`;
CREATE TABLE IF NOT EXISTS `codono_navigation` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `name` varchar(255) NOT NULL COMMENT 'name',
  `title` varchar(255) NOT NULL COMMENT 'name',
  `url` varchar(255) NOT NULL COMMENT 'url',
  `ico` varchar(30) DEFAULT NULL COMMENT 'Font awesome Icon',
  `sort` int(11) UNSIGNED NOT NULL COMMENT 'Sequence',
  `addtime` int(11) UNSIGNED NOT NULL COMMENT 'add time',
  `endtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Edit time',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_navigation`
--

INSERT INTO `codono_navigation` (`id`, `name`, `title`, `url`, `ico`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, 'finance', 'Finance', 'Finance/index', NULL, 1, 0, 0, -1),
(2, 'user', 'User', 'User/index', NULL, 2, 0, 0, -1),
(4, 'Blog', 'News', 'Article/index', 'newspaper-o', 7, 0, 0, 1),
(6, 'Store', 'Store', 'Shop/index', 'shopping-bag', 5, 0, 0, 1),
(7, 'Vote', 'Vote', 'Vote/index', 'trophy', 6, 0, 0, 1),
(8, 'ICO', 'ICO', 'Issue/index', NULL, 4, 1474183878, 0, -1),
(15, 'launchpad', 'Launchpad', 'launchpad', 'rocket', 3, 1522312746, 0, 0),
(16, 'System Health', 'Health', 'Content/health', 'heartbeat', 10, 1524302866, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `codono_notification`
--

DROP TABLE IF EXISTS `codono_notification`;
CREATE TABLE IF NOT EXISTS `codono_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'index',
  `to_email` varchar(255) DEFAULT NULL COMMENT 'Recepient',
  `subject` text COMMENT 'email subject',
  `content` text COMMENT 'email content',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=unsent, 1 =sent',
  `sent_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Request time',
  `priority` int(2) NOT NULL DEFAULT '0' COMMENT 'Experimental',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_paybyemail`
--

DROP TABLE IF EXISTS `codono_paybyemail`;
CREATE TABLE IF NOT EXISTS `codono_paybyemail` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_userid` int(11) UNSIGNED DEFAULT NULL,
  `to_userid` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL COMMENT 'user identification email',
  `coinname` varchar(200) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL COMMENT 'code for verification',
  `txid` varchar(50) DEFAULT NULL COMMENT 'system generated some hash',
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_userid` (`from_userid`),
  KEY `status` (`status`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_pool`
--

DROP TABLE IF EXISTS `codono_pool`;
CREATE TABLE IF NOT EXISTS `codono_pool` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `ico` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `tian` int(11) UNSIGNED DEFAULT NULL,
  `limit` varchar(50) DEFAULT NULL,
  `power` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Mining machine type table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_pool_log`
--

DROP TABLE IF EXISTS `codono_pool_log`;
CREATE TABLE IF NOT EXISTS `codono_pool_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `ico` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `tian` int(11) UNSIGNED DEFAULT NULL,
  `limit` varchar(50) DEFAULT NULL,
  `power` varchar(50) DEFAULT NULL,
  `num` int(11) UNSIGNED DEFAULT NULL,
  `use` int(11) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Minerals Management';

-- --------------------------------------------------------

--
-- Table structure for table `codono_prompt`
--

DROP TABLE IF EXISTS `codono_prompt`;
CREATE TABLE IF NOT EXISTS `codono_prompt` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `mytx` varchar(200) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_shop`
--

DROP TABLE IF EXISTS `codono_shop`;
CREATE TABLE IF NOT EXISTS `codono_shop` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `coinlist` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `buycoin` varchar(255) NOT NULL DEFAULT 'usd',
  `price` decimal(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `num` decimal(20,0) UNSIGNED NOT NULL DEFAULT '0',
  `deal` decimal(20,0) UNSIGNED NOT NULL DEFAULT '0',
  `content` text,
  `max` varchar(255) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `market_price` decimal(20,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Price',
  `codono_awardcoinnum` int(4) NOT NULL DEFAULT '0',
  `codono_awardcoin` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`),
  KEY `deal` (`deal`),
  KEY `price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shopping Center commercial table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_shop_addr`
--

DROP TABLE IF EXISTS `codono_shop_addr`;
CREATE TABLE IF NOT EXISTS `codono_shop_addr` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `truename` varchar(50) NOT NULL DEFAULT '0',
  `cellphone` varchar(500) DEFAULT NULL,
  `name` varchar(500) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_shop_coin`
--

DROP TABLE IF EXISTS `codono_shop_coin`;
CREATE TABLE IF NOT EXISTS `codono_shop_coin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `shopid` int(11) UNSIGNED NOT NULL COMMENT 'Productid',
  `usd` varchar(200) DEFAULT NULL,
  `btc` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Commodity payment table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_shop_log`
--

DROP TABLE IF EXISTS `codono_shop_log`;
CREATE TABLE IF NOT EXISTS `codono_shop_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) DEFAULT NULL,
  `shopid` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000',
  `coinname` varchar(50) NOT NULL DEFAULT '0.00',
  `num` decimal(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000',
  `mum` decimal(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000',
  `addr` varchar(50) NOT NULL DEFAULT '0.0000',
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `xuyao` decimal(20,8) UNSIGNED NOT NULL COMMENT 'price',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Shopping recording table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_shop_type`
--

DROP TABLE IF EXISTS `codono_shop_type`;
CREATE TABLE IF NOT EXISTS `codono_shop_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Categories';

--
-- Dumping data for table `codono_shop_type`
--

INSERT INTO `codono_shop_type` (`id`, `name`, `title`, `remark`, `sort`, `endtime`, `addtime`, `status`) VALUES
(1, 'health', 'Health', 'Health', 1, 1520524800, 1518105600, 1),
(2, 'electronics', 'Electronics', 'Electronics', 2, 1507651200, 1507651200, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_stop`
--

DROP TABLE IF EXISTS `codono_stop`;
CREATE TABLE IF NOT EXISTS `codono_stop` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT '0',
  `market` varchar(50) DEFAULT NULL,
  `compare` enum('lt','gt') NOT NULL DEFAULT 'lt' COMMENT 'lt=stop < current price, gt is stop  > current price',
  `price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `stop` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `num` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `deal` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `mum` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `fee` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `type` tinyint(2) UNSIGNED DEFAULT NULL COMMENT '1=Buy , 2 = Sale',
  `sort` int(11) UNSIGNED DEFAULT '0',
  `addtime` int(11) UNSIGNED DEFAULT '0',
  `endtime` int(11) UNSIGNED DEFAULT '0',
  `status` tinyint(2) DEFAULT '0' COMMENT '0=Pending , 1 = Processed ,2 =Cancelled',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `market` (`market`,`type`,`status`),
  KEY `num` (`num`,`deal`),
  KEY `status` (`status`),
  KEY `market_2` (`market`)
) ENGINE=MyISAM AUTO_INCREMENT=337 DEFAULT CHARSET=utf8 COMMENT='Stop market table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_text`
--

DROP TABLE IF EXISTS `codono_text`;
CREATE TABLE IF NOT EXISTS `codono_text` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `codono_text`
--

INSERT INTO `codono_text` (`id`, `name`, `title`, `content`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, 'vote', 'New Coin Voting', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>37 Please modify the content here in the background</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', 0, 1469733741, 0, 0),
(2, 'finance_index', 'Financial Center', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span><span style=\"color:#CC33E5;\">Financial Center</span><span style=\"color:#CC33E5;\"></span></span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\"><span style=\"color:#EE33EE;\"></span></span>', 0, 1475325266, 0, 1),
(3, 'finance_myzr', 'Deposit Coins', '<span style=\"color:#CC33E5;line-height:21px;background-color:#FFFFFF;\"><span style=\"color:#CC33E5;\"><span style=\"color:#CC33E5;font-family:sans-serif;line-height:normal;background-color:#FFFFFF;\">Virtual currency wallet address</span></span></span>', 0, 1475325312, 0, 1),
(4, 'finance_myzc', 'Withdraw Coins', '<p>\r\n	<span style=\"color:#CC33E5;font-family:sans-serif;font-size:14px;line-height:21px;background-color:#FFFFFF;\">Withdrawal Description:</span> \r\n</p>\r\n<p>\r\n	<span style=\"color:#333333;font-family:sans-serif;font-size:20px;line-height:21px;background-color:#FFFFFF;\"><span style=\"font-size:14px;color:#CC33E5;\">1: For safety, Withdrawals are processed manually</span><span style=\"font-size:14px;color:#CC33E5;\">, after you have submitted the request.</span></span>\r\n</p>\r\n<p>\r\n	<span style=\"color:#CC33E5;font-family:sans-serif;font-size:14px;line-height:21px;background-color:#FFFFFF;\">2: In order to speed up the currency transfer, we will pay the network fee for each transfer, but due to the uncertainty of the network, some remittances will still be slow, please be patient</span> \r\n</p>\r\n<p>\r\n	<span style=\"color:#CC33E5;font-family:sans-serif;font-size:12px;line-height:21px;background-color:#FFFFFF;\"><span style=\"font-size:14px;color:#CC33E5;\">3: Withdrawal service fee for each transaction is&nbsp;</span><span class=\"coin-feerate\" style=\"font-weight:700;font-family:sans-serif;color:#CC33E5;line-height:24px;font-size:14px;background-color:#FFFFFF;\">1.00%</span></span>\r\n</p>', 0, 1475325321, 0, 1),
(5, 'finance_mywt', 'My Orders', '<span style=\"color:#CC33E5;\"><span style=\"line-height:21px;color:#CC33E5;background-color:#FFFFFF;\">Please check your trust in this record</span></span>', 0, 1475325496, 0, 0),
(6, 'finance_mycj', 'My Transactions', '<span style=\"color:#9933E5;font-size:14px;\"><span style=\"line-height:21px;color:#9933E5;font-size:14px;background-color:#FFFFFF;\">Discover all the buying and selling transaction records</span></span>', 0, 1475325508, 0, 1),
(7, 'finance_mycz', 'Account Recharge', '<span style=\"color:#9933E5;line-height:21px;\"><span style=\"color:#9933E5;\"><span style=\"color:#9933E5;font-family:sans-serif;font-size:14px;line-height:34px;\">They are only allowed to use the Alipay</span></span></span><span style=\"color:#0096E0;line-height:21px;\"><span><span style=\"color:#FF0D00;font-family:sans-serif;font-size:14px;line-height:34px;\"><span style=\"color:#9933E5;\"></span><span style=\"color:#9933E5;\">Transfer recharge, make sure the senders name and account ID name registration agreement</span></span></span></span><span style=\"color:#9933E5;font-family:sans-serif;font-size:14px;line-height:34px;\">. Recharge limit100to50000, Recharge arrival time every morning:09Point to11point &nbsp;-16 Point to 18 Point, please do recharge prepare in advance</span>', 0, 1475325515, 0, 1),
(8, 'user_index', 'User Center', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span><span style=\"color:#CC33E5;line-height:21px;background-color:#FFFFFF;\">Please Safety Center bonding with your phone Alipay</span></span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\"><span style=\"color:#EE33EE;\"></span></span>', 0, 1475325658, 0, 1),
(9, 'finance_mytx', 'Account Withdrawal', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span> \r\n<h3 style=\"font-family:sans-serif;font-weight:500;font-size:24px;background-color:#FFFFFF;\">\r\n	<span style=\"color:#9933E5;\">Withdraw Notice</span> \r\n</h3>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	1. Withdrawal fee rate1%, Minimum charge per withdrawal2yuan .\r\n</p>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	2. Single cash withdrawal limit100yuan--50000yuan.\r\n</p>\r\n<p style=\"color:#999999;font-family:sans-serif;font-size:14px;background-color:#FFFFFF;\">\r\n	3. RMB cash withdrawal12Arrive within hours, it has been exported12After hours Have not received payment, please contact customer service.\r\n</p>\r\n</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\"><span style=\"color:#EE33EE;\"></span></span>', 0, 1475325679, 0, 1),
(10, 'user_cellphone', 'Mobile', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span> \n<h1 style=\"font-weight:normal;font-family:sans-serif;font-size:20px;color:#333333;background-color:#FFFFFF;\">\n	<span style=\"color:#9933E5;\">Please bind your convenience phone recharge and withdrawals</span> \n</h1>\n</span></span>', 0, 1475351892, 0, 1),
(11, 'finance_mytj', 'Refferal Code', '<span style=\"color:#9933E5;line-height:21px;font-size:14px;background-color:#FFFFFF;\"><span style=\"color:#9933E5;font-size:14px;\"><span style=\"color:#9933E5;font-family:sans-serif;line-height:30.8px;font-size:14px;background-color:#FFFFFF;\">This is your special invitation code:<span style=\"color:#666666;font-family:sans-serif;font-size:14px;line-height:normal;background-color:#FFFFFF;\">The amount of three 	 rewards offline:generation0.3% II0.2% Three generations0.1</span></span></span></span>', 0, 1475352280, 0, 1),
(12, 'finance_mywd', 'My Referrals', '<span style=\"color:#CC33E5;\"><span style=\"line-height:21px;color:#CC33E5;background-color:#FFFFFF;\">Personnel information query their own homes under the recommended</span></span>', 0, 1475352284, 0, 1),
(13, 'finance_myjp', 'My Rewards', '<span><span style=\"line-height:21px;color:#CC33E5;background-color:#FFFFFF;\">Sub-level recharge reward viewing</span></span><span style=\"color:#CC33E5;\"></span>', 0, 1475352285, 0, 1),
(14, 'issue', 'ICO Center', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">Currency coins crowdfunding record time:</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">12</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">month</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">12</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">day</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">20:00</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">to</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">12</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">month</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">15</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">day</span><span style=\"font-family:sans-serif;color:#CC33E5;line-height:30px;background-color:#FFFFFF;\">22:00</span><span style=\"color:#CC33E5;font-family:sans-serif;line-height:30px;background-color:#FFFFFF;\">For four days</span></span></span><span style=\"color:#0096E0;line-height:21px;\"><span><span style=\"color:#E53333;font-family:sans-serif;line-height:30px;\"><span style=\"font-size:16px;color:#E53333;\">(Total purchase limit200Wcurrency)</span></span></span></span>', 0, 1475352288, 0, 1),
(15, 'issue_log', 'ICO records', '<span><span style=\"line-height:21px;background-color:#FFFFFF;\">Check your ICO number of records</span></span>', 0, 1475352293, 0, 1),
(16, 'game_dividend', '20', '<br />', 0, 1475352294, 0, 0),
(17, 'game_dividend_log', 'Dividend Record', 'Hold XYZ coin every day and get dividend in %', 0, 1475352296, 0, 1),
(47, 'game_shop_log', NULL, '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>please atBackstagemodifyHerecontent</span></span><span style=\"color:#0096E0;line-height:21px;font-family:\'sans-serif\', \'Sim sun\', tahoma, \'Helvetica,Neue\', Helvetica, STHeiTi, Arial, sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', NULL, 1565261915, NULL, 1),
(18, 'game_money', '18', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>18Please modify the content here in the background</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', 0, 1475352297, 0, 0),
(19, 'game_money_log', '17', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>17Please modify the content here in the background</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', 0, 1475352298, 0, 0),
(20, 'user_paypassword', 'Funding Password', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span> \r\n<h1 style=\"font-weight:normal;font-family:sans-serif;font-size:20px;color:#333333;background-color:#FFFFFF;\">\r\n	<span style=\"color:#9933E5;\">Please edit your transaction password</span> \r\n</h1>\r\n</span></span>', 0, 1475352694, 0, 1),
(21, 'user_password', '69', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>Please modify the content here in the background</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', 0, 1475352695, 0, 0),
(22, 'user_nameauth', 'KYC', '<span style=\"color:#9933E5;\"><span style=\"line-height:21px;color:#9933E5;background-color:#FFFFFF;\">Please register your real name if error, please contact customer service to modify</span></span>', 0, 1475352696, 0, 1),
(23, 'user_tpwdset', 'Transaction password input settings', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>\r\n<h1 style=\"font-weight:normal;font-family:sans-serif;font-size:20px;color:#333333;background-color:#FFFFFF;\">\r\n	<span style=\"color:#CC33E5;\">Keep in mind that your transaction password</span>\r\n</h1>\r\n</span></span>', 0, 1475352698, 0, 1),
(24, 'shop_index', '13', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>13Please modify the content here in the background</span></span><span style=\"color:#0096E0;line-height:21px;font-family:sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', 0, 1475352702, 0, 0),
(25, 'issue_buy', '12', 'Edit this content from Backend', 0, 1475352722, 0, 0),
(38, 'index_warning', '1 B', 'We have disabled POW deposits for maintenance', NULL, NULL, NULL, 1),
(37, 'index_info', '1A', 'We have added a new currency set today Check <a href=\"/trade/index/market/ltc_usd/\">LTC/USD</a>', NULL, NULL, NULL, 1),
(26, 'game_topup', 'Prepaid recharge', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>\r\n<h1 style=\"font-weight:normal;font-family:sans-serif;font-size:20px;color:#333333;background-color:#FFFFFF;\">\r\n	<span style=\"color:#CC33E5;\">Prepaid recharge is not currently open</span>\r\n</h1>\r\n</span></span>', 0, 1475359119, 0, 0),
(27, 'user_bank', 'Bank management', '<span style=\"font-size:14px;color:#CC33E5;\">Please bind your PayPal a binding but can not be modified</span>', 0, 1475359192, 0, 1),
(28, 'user_wallet', 'Wallet address management', '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>\r\n<h1 style=\"font-weight:normal;font-family:sans-serif;font-size:20px;color:#333333;background-color:#FFFFFF;\">\r\n	<span style=\"color:#CC33E5;\">It is not providing turn into a wallet</span>\r\n</h1>\r\n</span></span>', 0, 1475359195, 0, 1),
(29, 'user_log', 'My Logs', 'You can modify this text from backend!', 0, 1475359241, 0, 0),
(46, 'game_shop_view', NULL, '<span style=\"color:#0096E0;line-height:21px;background-color:#FFFFFF;\"><span>please atBackstagemodifyHerecontent</span></span><span style=\"color:#0096E0;line-height:21px;font-family:\'sans-serif\', \'Sim sun\', tahoma, \'Helvetica,Neue\', Helvetica, STHeiTi, Arial, sans-serif;background-color:#FFFFFF;\">,<span style=\"color:#EE33EE;\">details</span></span>', NULL, 1554555254, NULL, 1),
(30, 'user_ga', '2FA Setup\r\n', 'You can modify this text from backend!', 0, 1475395398, 0, 0),
(31, 'user_alipay', 'Binding Alipay', '<span style=\"color:#CC33E5;line-height:21px;background-color:#FFFFFF;\"><span style=\"color:#CC33E5;\"><span style=\"color:#CC33E5;font-family:sans-serif;line-height:34px;background-color:#FFFFFF;\">Please bind your real Alipay</span></span></span>', 0, 1475395410, 0, 1),
(32, 'user_goods', 'Address Management', 'You can modify this text from backend!', 0, 1475395413, 0, 1),
(33, 'shop_view', '3', 'You can modify this text from backend!', 0, 1476000366, 0, 0),
(34, 'shop_log', '2', 'You can modify this text from backend!', 0, 1476002906, 0, 0),
(35, 'shop_goods', '1', 'You can modify this text from backend!', 0, 1476002907, 0, 0),
(36, 'finance_myaward', '', 'You can modify this text from backend!', 0, 1482927855, 0, 1),
(43, 'game_issue_buy', NULL, 'You can modify this text from backend!', NULL, 1536848740, NULL, 1),
(39, 'game_bazaar', 'Android', 'Please check our latest mobile app!', NULL, 1523003258, NULL, 1),
(40, 'game_bazaar_mycj', NULL, 'You can modify this text from backend!', NULL, 1523003290, NULL, 1),
(44, 'game_issue_log', NULL, 'You can modify this text from backend!', NULL, 1542363500, NULL, 1),
(41, 'game_vote', NULL, 'You can modify this text from backend!', NULL, 1536420134, NULL, 1),
(42, 'game_issue', NULL, 'You can modify this text from backend!', NULL, 1536848733, NULL, 1),
(45, 'game_shop', NULL, 'You can modify this text from backend!', NULL, 1542450549, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_topup`
--

DROP TABLE IF EXISTS `codono_topup`;
CREATE TABLE IF NOT EXISTS `codono_topup` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `cellphone` varchar(255) DEFAULT NULL,
  `num` int(11) UNSIGNED DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `mum` decimal(20,8) DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_topup_coin`
--

DROP TABLE IF EXISTS `codono_topup_coin`;
CREATE TABLE IF NOT EXISTS `codono_topup_coin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `coinname` varchar(50) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_topup_coin`
--

INSERT INTO `codono_topup_coin` (`id`, `coinname`, `price`, `status`) VALUES
(1, 'eos', '10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_topup_type`
--

DROP TABLE IF EXISTS `codono_topup_type`;
CREATE TABLE IF NOT EXISTS `codono_topup_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_topup_type`
--

INSERT INTO `codono_topup_type` (`id`, `name`, `title`, `status`) VALUES
(1, '10', '10Yuan prepaid recharge', 1),
(2, '20', '20Yuan prepaid recharge', 1),
(3, '30', '30Yuan prepaid recharge', 1),
(4, '50', '50Yuan prepaid recharge', 1),
(5, '100', '100Yuan prepaid recharge', 1),
(6, '300', '300Yuan prepaid recharge', 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_trade`
--

DROP TABLE IF EXISTS `codono_trade`;
CREATE TABLE IF NOT EXISTS `codono_trade` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT '0',
  `market` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `num` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `deal` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `mum` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `fee` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `type` tinyint(2) UNSIGNED DEFAULT NULL COMMENT '1=Buy , 2 = Sale',
  `sort` int(11) UNSIGNED DEFAULT '0',
  `addtime` int(11) UNSIGNED DEFAULT '0',
  `endtime` int(11) UNSIGNED DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `flag` int(40) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `market` (`market`,`type`,`status`),
  KEY `num` (`num`,`deal`),
  KEY `status` (`status`),
  KEY `market_2` (`market`)
) ENGINE=MyISAM AUTO_INCREMENT=16164 DEFAULT CHARSET=utf8 COMMENT='Under a single transaction table';

--
-- Dumping data for table `codono_trade`
--

INSERT INTO `codono_trade` (`id`, `userid`, `market`, `price`, `num`, `deal`, `mum`, `fee`, `type`, `sort`, `addtime`, `endtime`, `status`, `flag`) VALUES
(16160, 65, 'btc_usd', '17500.00000000', '0.02000000', '0.00000000', '350.00000000', '0.00000000', 2, 0, 1582719353, 0, 2, 0),
(16161, 65, 'btc_usd', '17360.00000000', '0.10000000', '0.00000000', '1736.00000000', '0.00000000', 1, 0, 1582719797, 0, 2, 0),
(16162, 65, 'btc_usd', '17500.00000000', '0.02000000', '0.00000000', '350.00000000', '0.00000000', 2, 0, 1582720199, 0, 2, 0),
(16163, 65, 'btc_usd', '17501.00000000', '0.10000000', '0.00000000', '1750.10000000', '0.00000000', 2, 0, 1582720221, 0, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `codono_trade_json`
--

DROP TABLE IF EXISTS `codono_trade_json`;
CREATE TABLE IF NOT EXISTS `codono_trade_json` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `market` varchar(100) DEFAULT NULL,
  `data` varchar(500) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `market` (`market`)
) ENGINE=MyISAM AUTO_INCREMENT=44743 DEFAULT CHARSET=utf8 COMMENT='Trading chart table';

-- --------------------------------------------------------

--
-- Table structure for table `codono_trade_log`
--

DROP TABLE IF EXISTS `codono_trade_log`;
CREATE TABLE IF NOT EXISTS `codono_trade_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `peerid` int(11) UNSIGNED DEFAULT NULL,
  `market` varchar(50) DEFAULT NULL,
  `price` decimal(20,8) UNSIGNED DEFAULT NULL,
  `num` decimal(20,8) UNSIGNED DEFAULT NULL,
  `mum` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee_buy` decimal(20,8) UNSIGNED DEFAULT NULL,
  `fee_sell` decimal(20,8) UNSIGNED DEFAULT NULL,
  `type` tinyint(2) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`),
  KEY `peerid` (`peerid`),
  KEY `main` (`market`,`status`,`addtime`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=274 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_ucenter_member`
--

DROP TABLE IF EXISTS `codono_ucenter_member`;
CREATE TABLE IF NOT EXISTS `codono_ucenter_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_user`
--

DROP TABLE IF EXISTS `codono_user`;
CREATE TABLE IF NOT EXISTS `codono_user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `cellphones` varchar(10) NOT NULL DEFAULT '+1',
  `cellphone` varchar(50) DEFAULT NULL,
  `cellphonetime` int(11) UNSIGNED DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `tpwdsetting` varchar(32) DEFAULT NULL,
  `paypassword` varchar(32) DEFAULT NULL,
  `invit_1` varchar(50) DEFAULT NULL,
  `invit_2` varchar(50) DEFAULT NULL,
  `invit_3` varchar(50) DEFAULT NULL,
  `truename` varchar(32) DEFAULT NULL,
  `applicantid` varchar(50) NOT NULL DEFAULT '0' COMMENT 'KYC applicantid for sumsub',
  `idcard` varchar(32) DEFAULT NULL,
  `idcardauth` tinyint(1) NOT NULL DEFAULT '0',
  `kyc_comment` varchar(250) DEFAULT NULL COMMENT 'If kyc was rejected then why?',
  `idcardimg1` varchar(100) DEFAULT NULL,
  `idcardimg2` varchar(100) DEFAULT NULL,
  `idcardinfo` varchar(500) DEFAULT NULL,
  `logins` int(11) NOT NULL DEFAULT '0' COMMENT 'Login Counts',
  `ga` varchar(50) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  `addr` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL COMMENT 'mailbox',
  `alipay` varchar(200) DEFAULT NULL COMMENT 'Alipay',
  `invit` varchar(50) DEFAULT NULL,
  `token` varchar(50) DEFAULT NULL,
  `apikey` varchar(50) DEFAULT NULL,
  `awardid` int(2) NOT NULL DEFAULT '0',
  `awardstatus` smallint(1) NOT NULL DEFAULT '0',
  `awardname` varchar(100) DEFAULT NULL,
  `awardNumAll` int(10) NOT NULL DEFAULT '0',
  `awardNumToday` int(10) NOT NULL DEFAULT '0',
  `awardTotalToday` int(10) NOT NULL DEFAULT '0',
  `awardtime` int(11) NOT NULL DEFAULT '0',
  `regaward` tinyint(1) NOT NULL DEFAULT '0',
  `usertype` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='User information table';

--
-- Dumping data for table `codono_user`
--

INSERT INTO `codono_user` (`id`, `username`, `cellphones`, `cellphone`, `cellphonetime`, `password`, `tpwdsetting`, `paypassword`, `invit_1`, `invit_2`, `invit_3`, `truename`, `applicantid`, `idcard`, `idcardauth`, `kyc_comment`, `idcardimg1`, `idcardimg2`, `idcardinfo`, `logins`, `ga`, `addip`, `addr`, `sort`, `addtime`, `endtime`, `status`, `email`, `alipay`, `invit`, `token`, `awardid`, `awardstatus`, `awardname`, `awardNumAll`, `awardNumToday`, `awardTotalToday`, `awardtime`, `regaward`, `usertype`) VALUES
(65, 'amber', '+1', '', 0, '5f4dcc3b5aa765d61d8327deb882cf99', NULL, '95c359186ccd09eaa3a1f18c791b1c36', '', '', '', 'jj', '0', 'jj', 3, 'Upload correct images', '5e4be712c6b5c.png_5e4be71940a71.png_5e4be72071301.png', NULL, 'jj', 10, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'amber@codono.com', NULL, 'WRHUNE', 'e4a14dc2077cdb97ede600a3f8326ba2', 0, 0, 'No prizes', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_award`
--

DROP TABLE IF EXISTS `codono_user_award`;
CREATE TABLE IF NOT EXISTS `codono_user_award` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(10) NOT NULL DEFAULT '0',
  `awardname` varchar(100) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `dealtime` int(11) NOT NULL DEFAULT '0',
  `awardid` int(2) NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_bank`
--

DROP TABLE IF EXISTS `codono_user_bank`;
CREATE TABLE IF NOT EXISTS `codono_user_bank` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `bank` varchar(200) DEFAULT NULL,
  `bankprov` varchar(200) DEFAULT NULL,
  `bankcity` varchar(200) DEFAULT NULL,
  `bankaddr` varchar(200) DEFAULT NULL,
  `bankcard` varchar(200) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_bank_type`
--

DROP TABLE IF EXISTS `codono_user_bank_type`;
CREATE TABLE IF NOT EXISTS `codono_user_bank_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT 'bank' COMMENT 'bank,crypto,paypal,others',
  `name` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `mytx` varchar(200) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='Common Bank Address';

--
-- Dumping data for table `codono_user_bank_type`
--

INSERT INTO `codono_user_bank_type` (`id`, `type`, `name`, `title`, `url`, `img`, `mytx`, `remark`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, 'bank', 'boc', 'Bank of China', 'http://www.boc.cn/', 'img_56937003683ce.jpg', '', '', 0, 1452503043, 0, 1),
(2, 'bank', 'abc', 'ABC', 'http://www.abchina.com/cn/', 'img_569370458b18d.jpg', '', '', 0, 1452503109, 0, 1),
(3, 'bank', 'bccb', 'Bank of Beijing', 'http://www.bankofbeijing.com.cn/', 'img_569370588dcdc.jpg', '', '', 0, 1452503128, 0, 1),
(4, 'bank', 'ccb', 'Construction Bank', 'http://www.ccb.com/', 'img_5693709bbd20f.jpg', '', '', 0, 1452503195, 0, 1),
(5, 'bank', 'ceb', 'China Everbright Bank', 'http://www.bankofbeijing.com.cn/', 'img_569370b207cc8.jpg', '', '', 0, 1452503218, 0, 1),
(6, 'bank', 'cib', 'Industrial Bank', 'http://www.cib.com.cn/cn/index.html', 'img_569370d29bf59.jpg', '', '', 0, 1452503250, 0, 1),
(7, 'bank', 'citic', 'CITIC Bank', 'http://www.ecitic.com/', 'img_569370fb7a1b3.jpg', '', '', 0, 1452503291, 0, 1),
(8, 'bank', 'cmb', 'China Merchants Bank', 'http://www.cmbchina.com/', 'img_5693710a9ac9c.jpg', '', '', 0, 1452503306, 0, 1),
(9, 'bank', 'cmbc', 'Minsheng Bank', 'http://www.cmbchina.com/', 'img_5693711f97a9d.jpg', '', '', 0, 1452503327, 0, 1),
(10, 'bank', 'comm', 'Bank of Communications', 'http://www.bankcomm.com/BankCommSite/default.shtml', 'img_5693713076351.jpg', '', '', 0, 1452503344, 0, 1),
(11, 'bank', 'gdb', 'Guangdong Development Bank', 'http://www.cgbchina.com.cn/', 'img_56937154bebc5.jpg', '', '', 0, 1452503380, 0, 1),
(12, 'bank', 'icbc', 'ICBC', 'http://www.icbc.com.cn/icbc/', 'img_56937162db7f5.jpg', '', '', 0, 1452503394, 0, 1),
(13, 'bank', 'psbc', 'Postal bank', 'http://www.psbc.com/portal/zh_CN/index.html', 'img_5693717eefaa3.jpg', '', '', 0, 1452503422, 0, 1),
(14, 'bank', 'spdb', 'Shanghai Pudong Development Bank', 'http://www.spdb.com.cn/chpage/c1/', 'img_5693718f1d70e.jpg', '', '', 0, 1452503439, 0, 1),
(15, 'bank', 'szpab', 'Ping An Bank', 'http://bank.pingan.com/', '56c2e4c9aff85.jpg', '', '', 0, 1455613129, 0, 1),
(16, 'bank', 'alipay', 'Alipay', 'http://www.alipay.com', '', '', '', 1, 1452503439, 0, 1),
(17, 'bank', 'BOA', 'Bank of America', 'https://www.bankofamerica.com', '', NULL, NULL, 2, NULL, NULL, 1),
(18, 'crypto', 'USD Tether', 'USDT', 'https://tether.to/', '', NULL, NULL, 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_coin`
--

DROP TABLE IF EXISTS `codono_user_coin`;
CREATE TABLE IF NOT EXISTS `codono_user_coin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED DEFAULT NULL,
  `usd` decimal(20,8) DEFAULT '0.00000000',
  `usdd` decimal(20,8) DEFAULT '0.00000000',
  `btc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `btcd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `btcb` varchar(200) DEFAULT NULL,
  `eth` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ethd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ethb` varchar(200) DEFAULT NULL,
  `etc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `etcd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `etcb` varchar(200) DEFAULT NULL,
  `ltc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ltcd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ltcb` varchar(200) DEFAULT NULL,
  `bcc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `bccd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `bccb` varchar(200) DEFAULT NULL,
  `ast` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `astd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `astb` varchar(200) DEFAULT NULL,
  `mtc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `mtcd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `mtcb` varchar(200) DEFAULT NULL,
  `eos` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `eosd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `eosb` varchar(200) DEFAULT NULL,
  `ico` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `icod` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `icob` varchar(200) DEFAULT NULL,
  `powr` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `powrd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `powrb` varchar(200) DEFAULT NULL,
  `doge` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `doged` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `dogeb` varchar(200) DEFAULT NULL,
  `tbtc` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `tbtcd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `tbtcb` varchar(200) DEFAULT NULL,
  `xrp` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `xrpd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `xrpb` varchar(200) DEFAULT NULL,
  `waves` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `wavesd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `wavesb` varchar(200) DEFAULT NULL,
  `ltct` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ltctd` decimal(20,8) UNSIGNED DEFAULT '0.00000000',
  `ltctb` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='Users denomination table';

--
-- Dumping data for table `codono_user_coin`
--

INSERT INTO `codono_user_coin` (`id`, `userid`, `usd`, `usdd`, `btc`, `btcd`, `btcb`, `eth`, `ethd`, `ethb`, `etc`, `etcd`, `etcb`, `ltc`, `ltcd`, `ltcb`, `bcc`, `bccd`, `bccb`, `ast`, `astd`, `astb`, `mtc`, `mtcd`, `mtcb`, `eos`, `eosd`, `eosb`, `ico`, `icod`, `icob`, `powr`, `powrd`, `powrb`, `doge`, `doged`, `dogeb`, `tbtc`, `tbtcd`, `tbtcb`, `xrp`, `xrpd`, `xrpb`, `waves`, `wavesd`, `wavesb`, `ltct`, `ltctd`, `ltctb`) VALUES
(57, 65, '40000.00000000', '0.00000000', '1.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(58, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(59, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(60, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(61, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(62, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(63, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(64, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(65, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(66, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(67, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(68, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(69, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(70, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL),
(71, 65, '0.00000000', '0.00000000', '0.10000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL, '0.00000000', '0.00000000', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_goods`
--

DROP TABLE IF EXISTS `codono_user_goods`;
CREATE TABLE IF NOT EXISTS `codono_user_goods` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `truename` varchar(200) DEFAULT NULL,
  `idcard` varchar(200) DEFAULT NULL,
  `cellphone` varchar(200) DEFAULT NULL,
  `addr` varchar(200) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  `prov` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_log`
--

DROP TABLE IF EXISTS `codono_user_log`;
CREATE TABLE IF NOT EXISTS `codono_user_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(200) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `addip` varchar(200) DEFAULT NULL,
  `addr` varchar(200) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(10) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=872 DEFAULT CHARSET=utf8 COMMENT='Users record sheet';

--
-- Dumping data for table `codono_user_log`
--

INSERT INTO `codono_user_log` (`id`, `userid`, `type`, `remark`, `addip`, `addr`, `sort`, `addtime`, `endtime`, `status`) VALUES
(862, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1581412864, NULL, 1),
(863, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1581415546, NULL, 1),
(864, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1581416401, NULL, 1),
(865, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582032597, NULL, 1),
(866, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582564736, NULL, 1),
(867, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582719255, NULL, 1),
(868, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582719257, NULL, 1),
(869, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582870285, NULL, 1),
(870, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582870722, NULL, 1),
(871, 65, 'LOG IN', 'LOGIN WITH USERNAME', '0.0.0.0', 'Unassigned or Local IP', NULL, 1582967808, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_shopaddr`
--

DROP TABLE IF EXISTS `codono_user_shopaddr`;
CREATE TABLE IF NOT EXISTS `codono_user_shopaddr` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `truename` varchar(200) NOT NULL DEFAULT '0',
  `cellphone` varchar(500) DEFAULT NULL,
  `name` varchar(500) DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_subscription`
--

DROP TABLE IF EXISTS `codono_user_subscription`;
CREATE TABLE IF NOT EXISTS `codono_user_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `subid` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) DEFAULT NULL,
  `endtime` int(10) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `codono_user_subscription`
--

INSERT INTO `codono_user_subscription` (`id`, `uid`, `subid`, `addtime`, `endtime`, `status`) VALUES
(2, 65, 1, 1581422216, 1597147016, 1),
(3, 65, 2, 1581423467, 1597148267, 1),
(4, 65, 1, 1581423775, 1597148575, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_user_wallet`
--

DROP TABLE IF EXISTS `codono_user_wallet`;
CREATE TABLE IF NOT EXISTS `codono_user_wallet` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `coinname` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `addr` varchar(200) DEFAULT NULL,
  `dest_tag` varchar(200) DEFAULT NULL COMMENT 'for xrp or xmr',
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userid` (`userid`),
  KEY `coinname` (`coinname`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Users wallet table';

--
-- Dumping data for table `codono_user_wallet`
--

INSERT INTO `codono_user_wallet` (`id`, `userid`, `coinname`, `name`, `addr`, `dest_tag`, `sort`, `addtime`, `endtime`, `status`) VALUES
(29, 65, 'eos', 'internal', '9f69b6df7436c4d388ee561c5167de42', '', NULL, 1582870787, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_verify`
--

DROP TABLE IF EXISTS `codono_verify`;
CREATE TABLE IF NOT EXISTS `codono_verify` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'index',
  `email` varchar(100) DEFAULT NULL COMMENT 'email',
  `code` varchar(10) DEFAULT NULL COMMENT 'verification code',
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when',
  `attempts` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'number of attempts',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codono_version`
--

DROP TABLE IF EXISTS `codono_version`;
CREATE TABLE IF NOT EXISTS `codono_version` (
  `name` varchar(50) NOT NULL COMMENT 'Version number',
  `number` int(11) NOT NULL COMMENT 'Serial number, date generally designated by numeral',
  `title` varchar(50) NOT NULL COMMENT 'Version name',
  `create_time` int(11) NOT NULL COMMENT 'release time',
  `update_time` int(11) NOT NULL COMMENT 'Update of time',
  `log` text NOT NULL COMMENT 'Update Log',
  `url` varchar(150) NOT NULL COMMENT 'Link to a remote article',
  `is_current` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `id` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Automatic Updates table' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_version0`
--

DROP TABLE IF EXISTS `codono_version0`;
CREATE TABLE IF NOT EXISTS `codono_version0` (
  `name` varchar(50) NOT NULL COMMENT 'Version number',
  `number` int(11) NOT NULL COMMENT 'Serial number, date generally designated by numeral',
  `title` varchar(50) NOT NULL COMMENT 'Version name',
  `create_time` int(11) NOT NULL COMMENT 'release time',
  `update_time` int(11) NOT NULL COMMENT 'Update of time',
  `log` text NOT NULL COMMENT 'Update Log',
  `url` varchar(150) NOT NULL COMMENT 'Link to a remote article',
  `is_current` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `id` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Automatic Updates table' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codono_version_game`
--

DROP TABLE IF EXISTS `codono_version_game`;
CREATE TABLE IF NOT EXISTS `codono_version_game` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `gongsi` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `shuoming` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `class` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `number` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application management table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_version_game`
--

INSERT INTO `codono_version_game` (`id`, `gongsi`, `shuoming`, `class`, `name`, `title`, `status`) VALUES
(1, 'YourExchangeV2', 'online store', 'shop', 'shop', 'online store', 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_version_game0`
--

DROP TABLE IF EXISTS `codono_version_game0`;
CREATE TABLE IF NOT EXISTS `codono_version_game0` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `gongsi` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `shuoming` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `class` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `number` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application management table' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_version_game0`
--

INSERT INTO `codono_version_game0` (`id`, `gongsi`, `shuoming`, `class`, `name`, `title`, `status`) VALUES
(1, 'YourExchangeV2', 'online store', 'shop', 'shop', 'online store', 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_vote`
--

DROP TABLE IF EXISTS `codono_vote`;
CREATE TABLE IF NOT EXISTS `codono_vote` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `coinname` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `type` int(20) UNSIGNED DEFAULT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `addtime` int(11) UNSIGNED DEFAULT NULL,
  `endtime` int(11) UNSIGNED DEFAULT NULL,
  `status` int(4) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `codono_vote`
--

INSERT INTO `codono_vote` (`id`, `userid`, `coinname`, `title`, `type`, `sort`, `addtime`, `endtime`, `status`) VALUES
(1, 30, 'Recox', 'Recox', 1, NULL, 1523273210, NULL, 1),
(2, 30, 'Recox', 'Recox', 1, NULL, 1523273215, NULL, 1),
(3, 30, 'Recox', 'Recox', 1, NULL, 1523273365, NULL, 1),
(4, 30, 'Recox', 'Recox', 1, NULL, 1523274858, NULL, 1),
(5, 30, 'Recox', 'Recox', 1, NULL, 1523275144, NULL, 1),
(6, 30, 'Recox', 'Recox', 1, NULL, 1523275159, NULL, 1),
(7, 1, 'NEO', 'NEO', 1, NULL, 1526045968, NULL, 1),
(8, 1, 'XMR', 'Monero', 1, NULL, 1526045975, NULL, 1),
(9, 1, 'PART', 'Particl', 2, NULL, 1526046294, NULL, 1),
(10, 1, 'IOTA', 'IOTA', 1, NULL, 1526046817, NULL, 1),
(11, 1, 'IOTA', 'IOTA', 1, NULL, 1526046825, NULL, 1),
(12, 1, 'NXT', 'nxt', 1, NULL, 1526046845, NULL, 1),
(13, 38, 'NEO', 'NEO', 1, NULL, 1569841024, NULL, 1),
(14, 38, 'Recox', 'Recox', 1, NULL, 1569841033, NULL, 1),
(15, 38, 'doge', NULL, 1, NULL, 1569841042, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `codono_vote_type`
--

DROP TABLE IF EXISTS `codono_vote_type`;
CREATE TABLE IF NOT EXISTS `codono_vote_type` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID AUTO INC',
  `coinname` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT 'status',
  `img` varchar(255) DEFAULT NULL,
  `zhichi` bigint(20) UNSIGNED DEFAULT '0',
  `fandui` bigint(20) UNSIGNED DEFAULT '0',
  `zongji` bigint(20) UNSIGNED DEFAULT '0',
  `bili` float DEFAULT '0',
  `votecoin` varchar(50) DEFAULT NULL,
  `assumnum` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DROP TABLE IF EXISTS `codono_investbox`;
CREATE TABLE IF NOT EXISTS `codono_investbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coinname` varchar(50) DEFAULT NULL,
  `percentage` decimal(5,3) NOT NULL DEFAULT '0.000' COMMENT 'roi',
  `period` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'daily=1,weekly=7,monthly=30',
  `minvest` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `maxvest` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `creatorid` int(30) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL DEFAULT '{"noaction":"1"}' COMMENT 'saved in json format, coin,market info',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT 'when added',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `codono_investbox`
--

INSERT INTO `codono_investbox` (`id`, `coinname`, `percentage`, `period`, `minvest`, `maxvest`, `creatorid`, `action`, `status`, `addtime`) VALUES
(7, 'xmr', '4.000', 30, '10.00000000', '1000.00000000', 0, '{\"noaction\":\"1\"}', 1, 0),
(9, 'eth', '5.000', 1, '0.03555500', '2.12500000', 0, '{\"coin\":{\"name\":\"ltc\",\"value\":\"1.5\"},\"market\":{\"name\":\"ltc_usd\",\"buy\":\"1\",\"sell\":\"2\"}}', 1, 0),
(12, 'eth', '5.000', 7, '10.00000000', '150.00000000', 0, '{\"coin\":{\"name\":\"btc\",\"value\":\"1\"},\"market\":{\"name\":\"ltc\",\"buy\":\"1\",\"sell\":\"11\"}}', 3, 0),
(16, 'powr', '5.000', 1, '10.00000000', '100.00000000', 38, '{\"coin\":{\"name\":\"powr\",\"value\":\"100\"},\"market\":{\"name\":\"ltc_usd\",\"buy\":\"10\",\"sell\":\"15\"}}', 0, 1569670103);
