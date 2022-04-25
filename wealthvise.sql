-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2021 at 12:38 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wealthvise`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` blob NOT NULL,
  `image` varchar(200) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `meta_tags` text DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `content`, `image`, `status`, `created_by`, `meta_tags`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Test', 0x3c703e3132333c2f703e0d0a0d0a3c703e266e6273703b3c2f703e, '1603791034_Koala.jpg', '1', 1, NULL, NULL, NULL, NULL, '2020-10-27 04:00:34', '2020-12-04 06:42:58', NULL),
(2, 'Tests', 0x3c703e7364616161616161616161616161616161613c2f703e, '1603791928_Tulips.jpg', '1', 1, 'ssssssssssssssss', 'sssssssss', 'sssssssss', 'ssssssssssssssssss', '2020-10-27 04:01:36', '2020-10-27 04:35:10', NULL),
(3, 'Test', 0x3c703e746573743c2f703e, '1603869116_Tulips.jpg', '1', 2, '3', NULL, NULL, NULL, '2020-10-28 01:41:56', '2020-10-28 01:42:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`) VALUES
(1, 32, 'Adilabad'),
(2, 34, 'Agra'),
(3, 21, 'Ahmed Nagar'),
(4, 12, 'Ahmedabad City'),
(5, 24, 'Aizawl'),
(6, 29, 'Ajmer'),
(7, 21, 'Akola'),
(8, 34, 'Aligarh'),
(9, 34, 'Allahabad'),
(10, 18, 'Alleppey'),
(11, 35, 'Almora'),
(12, 29, 'Alwar'),
(13, 18, 'Alwaye'),
(14, 2, 'Amalapuram'),
(15, 13, 'Ambala'),
(16, 34, 'Ambedkar Nagar'),
(17, 21, 'Amravati'),
(18, 12, 'Amreli'),
(19, 28, 'Amritsar'),
(20, 2, 'Anakapalle'),
(21, 12, 'Anand'),
(22, 2, 'Anantapur'),
(23, 15, 'Ananthnag'),
(24, 31, 'Anna Road H.O'),
(25, 31, 'Arakkonam'),
(26, 36, 'Asansol'),
(27, 26, 'Aska'),
(28, 34, 'Auraiya'),
(29, 21, 'Aurangabad'),
(30, 5, 'Aurangabad(Bihar)'),
(31, 34, 'Azamgarh'),
(32, 17, 'Bagalkot'),
(33, 35, 'Bageshwar'),
(34, 34, 'Bagpat'),
(35, 34, 'Bahraich'),
(36, 20, 'Balaghat'),
(37, 26, 'Balangir'),
(38, 26, 'Balasore'),
(39, 34, 'Ballia'),
(40, 34, 'Balrampur'),
(41, 12, 'Banasanktha'),
(42, 34, 'Banda'),
(43, 15, 'Bandipur'),
(44, 36, 'Bankura'),
(45, 29, 'Banswara'),
(46, 34, 'Barabanki'),
(47, 15, 'Baramulla'),
(48, 29, 'Baran'),
(49, 12, 'Bardoli'),
(50, 34, 'Bareilly'),
(51, 29, 'Barmer'),
(52, 28, 'Barnala'),
(53, 4, 'Barpeta'),
(54, 7, 'Bastar'),
(55, 34, 'Basti'),
(56, 28, 'Bathinda'),
(57, 21, 'Beed'),
(58, 5, 'Begusarai'),
(59, 17, 'Belgaum'),
(60, 17, 'Bellary'),
(61, 17, 'Bengaluru East'),
(62, 17, 'Bengaluru South'),
(63, 17, 'Bengaluru West'),
(64, 26, 'Berhampur'),
(65, 26, 'Bhadrak'),
(66, 5, 'Bhagalpur'),
(67, 21, 'Bhandara'),
(68, 29, 'Bharatpur'),
(69, 12, 'Bharuch'),
(70, 12, 'Bhavnagar'),
(71, 29, 'Bhilwara'),
(72, 2, 'Bhimavaram'),
(73, 13, 'Bhiwani'),
(74, 5, 'Bhojpur'),
(75, 20, 'Bhopal'),
(76, 26, 'Bhubaneswar'),
(77, 17, 'Bidar'),
(78, 17, 'Bijapur'),
(79, 34, 'Bijnor'),
(80, 29, 'Bikaner'),
(81, 7, 'Bilaspur'),
(82, 36, 'Birbhum'),
(83, 22, 'Bishnupur'),
(84, 4, 'Bongaigaon'),
(85, 34, 'Budaun'),
(86, 15, 'Budgam'),
(87, 34, 'Bulandshahr'),
(88, 21, 'Buldhana'),
(89, 29, 'Bundi'),
(90, 36, 'Burdwan'),
(91, 4, 'Cachar'),
(92, 18, 'Calicut'),
(93, 1, 'Carnicobar'),
(94, 14, 'Chamba'),
(95, 35, 'Chamoli'),
(96, 35, 'Champawat'),
(97, 24, 'Champhai'),
(98, 34, 'Chandauli'),
(99, 22, 'Chandel'),
(100, 6, 'Chandigarh'),
(101, 21, 'Chandrapur'),
(102, 18, 'Changanacherry'),
(103, 3, 'Changlang'),
(104, 17, 'Channapatna'),
(105, 31, 'Chengalpattu'),
(106, 31, 'Chennai City Central'),
(107, 31, 'Chennai City North'),
(108, 31, 'Chennai City South'),
(109, 20, 'Chhatarpur'),
(110, 20, 'Chhindwara'),
(111, 17, 'Chikmagalur'),
(112, 17, 'Chikodi'),
(113, 17, 'Chitradurga'),
(114, 34, 'Chitrakoot'),
(115, 2, 'Chittoor'),
(116, 29, 'Chittorgarh'),
(117, 22, 'Churachandpur'),
(118, 29, 'Churu'),
(119, 31, 'Coimbatore'),
(120, 36, 'Contai'),
(121, 36, 'Cooch Behar'),
(122, 31, 'Cuddalore'),
(123, 2, 'Cuddapah'),
(124, 26, 'Cuttack City'),
(125, 26, 'Cuttack North'),
(126, 26, 'Cuttack South'),
(127, 8, 'Dadra & Nagar Haveli'),
(128, 9, 'Daman'),
(129, 5, 'Darbhanga'),
(130, 36, 'Darjiling'),
(131, 4, 'Darrang'),
(132, 29, 'Dausa'),
(133, 14, 'Dehra Gopipur'),
(134, 35, 'Dehradun'),
(135, 10, 'Delhi'),
(136, 34, 'Deoria'),
(137, 33, 'Dhalai'),
(138, 16, 'Dhanbad'),
(139, 14, 'Dharamsala'),
(140, 31, 'Dharmapuri'),
(141, 17, 'Dharwad'),
(142, 4, 'Dhemaji'),
(143, 26, 'Dhenkanal'),
(144, 29, 'Dholpur'),
(145, 4, 'Dhubri'),
(146, 21, 'Dhule'),
(147, 3, 'Dibang Valley'),
(148, 4, 'Dibrugarh'),
(149, 1, 'Diglipur'),
(150, 25, 'Dimapur'),
(151, 31, 'Dindigul'),
(152, 9, 'Diu'),
(153, 15, 'Doda'),
(154, 29, 'Dungarpur'),
(155, 7, 'Durg'),
(156, 5, 'East Champaran'),
(157, 23, 'East Garo Hills'),
(158, 3, 'East Kameng'),
(159, 23, 'East Khasi Hills'),
(160, 3, 'East Siang'),
(161, 30, 'East Sikkim'),
(162, 2, 'Eluru'),
(163, 18, 'Ernakulam'),
(164, 31, 'Erode'),
(165, 34, 'Etah'),
(166, 34, 'Etawah'),
(167, 34, 'Faizabad'),
(168, 13, 'Faridabad'),
(169, 28, 'Faridkot'),
(170, 34, 'Farrukhabad'),
(171, 28, 'Fatehgarh Sahib'),
(172, 34, 'Fatehpur'),
(173, 28, 'Fazilka'),
(174, 1, 'Ferrargunj'),
(175, 34, 'Firozabad'),
(176, 28, 'Firozpur'),
(177, 17, 'Gadag'),
(178, 21, 'Gadchiroli'),
(179, 12, 'Gandhinagar'),
(180, 29, 'Ganganagar'),
(181, 34, 'Gautam Buddha Nagar'),
(182, 5, 'Gaya'),
(183, 34, 'Ghaziabad'),
(184, 34, 'Ghazipur'),
(185, 16, 'Giridih'),
(186, 11, 'Goa'),
(187, 4, 'Goalpara'),
(188, 17, 'Gokak'),
(189, 4, 'Golaghat'),
(190, 34, 'Gonda'),
(191, 12, 'Gondal'),
(192, 21, 'Gondia'),
(193, 34, 'Gorakhpur'),
(194, 2, 'Gudivada'),
(195, 2, 'Gudur'),
(196, 17, 'Gulbarga'),
(197, 20, 'Guna'),
(198, 2, 'Guntur'),
(199, 28, 'Gurdaspur'),
(200, 13, 'Gurugram'),
(201, 20, 'Gwalior'),
(202, 4, 'Hailakandi'),
(203, 14, 'Hamirpur (HP)'),
(204, 34, 'Hamirpur (UP)'),
(205, 32, 'Hanamkonda'),
(206, 29, 'Hanumangarh'),
(207, 34, 'Hardoi'),
(208, 35, 'Haridwar'),
(209, 17, 'Hassan'),
(210, 34, 'Hathras'),
(211, 17, 'Haveri'),
(212, 16, 'Hazaribagh'),
(213, 2, 'Hindupur'),
(214, 21, 'Hingoli'),
(215, 13, 'Hissar'),
(216, 36, 'Hooghly'),
(217, 20, 'Hoshangabad'),
(218, 28, 'Hoshiarpur'),
(219, 36, 'Howrah'),
(220, 1, 'Hut Bay'),
(221, 32, 'Hyderabad City'),
(222, 32, 'Hyderabad South East'),
(223, 18, 'Idukki'),
(224, 22, 'Imphal East'),
(225, 22, 'Imphal West'),
(226, 20, 'Indore City'),
(227, 20, 'Indore Moffusil'),
(228, 18, 'Irinjalakuda'),
(229, 20, 'Jabalpur'),
(230, 23, 'Jaintia Hills'),
(231, 29, 'Jaipur'),
(232, 29, 'Jaisalmer'),
(233, 28, 'Jalandhar'),
(234, 34, 'Jalaun'),
(235, 21, 'Jalgaon'),
(236, 21, 'Jalna'),
(237, 29, 'Jalor'),
(238, 36, 'Jalpaiguri'),
(239, 15, 'Jammu'),
(240, 12, 'Jamnagar'),
(241, 34, 'Jaunpur'),
(242, 29, 'Jhalawar'),
(243, 34, 'Jhansi'),
(244, 29, 'Jhujhunu'),
(245, 29, 'Jodhpur'),
(246, 4, 'Jorhat'),
(247, 12, 'Junagadh'),
(248, 34, 'Jyotiba Phule Nagar'),
(249, 2, 'Kakinada'),
(250, 26, 'Kalahandi'),
(251, 4, 'Kamrup'),
(252, 31, 'Kanchipuram'),
(253, 34, 'Kannauj'),
(254, 31, 'Kanniyakumari'),
(255, 18, 'Kannur'),
(256, 34, 'Kanpur Dehat'),
(257, 34, 'Kanpur Nagar'),
(258, 28, 'Kapurthala'),
(259, 27, 'Karaikal'),
(260, 31, 'Karaikudi'),
(261, 29, 'Karauli'),
(262, 4, 'Karbi Anglong'),
(263, 15, 'Kargil'),
(264, 4, 'Karimganj'),
(265, 32, 'Karimnagar'),
(266, 13, 'Karnal'),
(267, 31, 'Karur'),
(268, 17, 'Karwar'),
(269, 18, 'Kasaragod'),
(270, 15, 'Kathua'),
(271, 34, 'Kaushambi'),
(272, 26, 'Keonjhar'),
(273, 2, 'Khammam (AP)'),
(274, 32, 'Khammam (TL)'),
(275, 20, 'Khandwa'),
(276, 12, 'Kheda'),
(277, 34, 'Kheri'),
(278, 25, 'Kiphire'),
(279, 17, 'Kodagu'),
(280, 25, 'Kohima'),
(281, 4, 'Kokrajhar'),
(282, 17, 'Kolar'),
(283, 24, 'Kolasib'),
(284, 21, 'Kolhapur'),
(285, 36, 'Kolkata'),
(286, 26, 'Koraput'),
(287, 29, 'Kota'),
(288, 18, 'Kottayam'),
(289, 31, 'Kovilpatti'),
(290, 31, 'Krishnagiri'),
(291, 15, 'Kulgam'),
(292, 31, 'Kumbakonam'),
(293, 15, 'Kupwara'),
(294, 2, 'Kurnool'),
(295, 13, 'Kurukshetra'),
(296, 3, 'Kurung Kumey'),
(297, 34, 'Kushinagar'),
(298, 12, 'Kutch'),
(299, 4, 'Lakhimpur'),
(300, 19, 'Lakshadweep'),
(301, 34, 'Lalitpur'),
(302, 21, 'Latur'),
(303, 24, 'Lawngtlai'),
(304, 15, 'Leh'),
(305, 3, 'Lohit'),
(306, 25, 'Longleng'),
(307, 3, 'Lower Subansiri'),
(308, 34, 'Lucknow'),
(309, 28, 'Ludhiana'),
(310, 24, 'Lunglei'),
(311, 2, 'Machilipatnam'),
(312, 5, 'Madhubani'),
(313, 31, 'Madurai'),
(314, 32, 'Mahabubnagar'),
(315, 34, 'Maharajganj'),
(316, 12, 'Mahesana'),
(317, 34, 'Mahoba'),
(318, 34, 'Mainpuri'),
(319, 36, 'Malda'),
(320, 24, 'Mammit'),
(321, 14, 'Mandi'),
(322, 20, 'Mandsaur'),
(323, 17, 'Mandya'),
(324, 17, 'Mangalore'),
(325, 18, 'Manjeri'),
(326, 28, 'Mansa'),
(327, 4, 'Marigaon'),
(328, 34, 'Mathura'),
(329, 34, 'Mau'),
(330, 18, 'Mavelikara'),
(331, 1, 'Mayabander'),
(332, 31, 'Mayiladuthurai'),
(333, 26, 'Mayurbhanj'),
(334, 32, 'Medak'),
(335, 34, 'Meerut'),
(336, 23, 'Meghalaya'),
(337, 36, 'Midnapore'),
(338, 34, 'Mirzapur'),
(339, 28, 'Moga'),
(340, 28, 'Mohali'),
(341, 25, 'Mokokchung'),
(342, 25, 'Mon'),
(343, 5, 'Monghyr'),
(344, 34, 'Moradabad'),
(345, 20, 'Morena'),
(346, 28, 'Muktsar'),
(347, 21, 'Mumbai'),
(348, 36, 'Murshidabad'),
(349, 34, 'Muzaffarnagar'),
(350, 5, 'Muzaffarpur'),
(351, 17, 'Mysore'),
(352, 36, 'Nadia'),
(353, 4, 'Nagaon'),
(354, 31, 'Nagapattinam'),
(355, 29, 'Nagaur'),
(356, 21, 'Nagpur'),
(357, 35, 'Nainital'),
(358, 5, 'Nalanda'),
(359, 4, 'Nalbari'),
(360, 32, 'Nalgonda'),
(361, 31, 'Namakkal'),
(362, 1, 'Nancorie'),
(363, 1, 'Nancowrie'),
(364, 21, 'Nanded'),
(365, 21, 'Nandurbar'),
(366, 2, 'Nandyal'),
(367, 17, 'Nanjangud'),
(368, 2, 'Narasaraopet'),
(369, 21, 'Nashik'),
(370, 12, 'Navsari'),
(371, 5, 'Nawadha'),
(372, 28, 'Nawanshahr'),
(373, 2, 'Nellore'),
(374, 31, 'Nilgiris'),
(375, 32, 'Nizamabad'),
(376, 36, 'North 24 Parganas'),
(377, 4, 'North Cachar Hills'),
(378, 36, 'North Dinajpur'),
(379, 30, 'North Sikkim'),
(380, 33, 'North Tripura'),
(381, 21, 'Osmanabad'),
(382, 18, 'Ottapalam'),
(383, 16, 'Palamau'),
(384, 18, 'Palghat'),
(385, 29, 'Pali'),
(386, 12, 'Panchmahals'),
(387, 3, 'Papum Pare'),
(388, 21, 'Parbhani'),
(389, 2, 'Parvathipuram'),
(390, 12, 'Patan'),
(391, 18, 'Pathanamthitta'),
(392, 28, 'Patiala'),
(393, 5, 'Patna'),
(394, 31, 'Pattukottai'),
(395, 35, 'Pauri Garhwal'),
(396, 32, 'Peddapalli'),
(397, 25, 'Peren'),
(398, 25, 'Phek'),
(399, 26, 'Phulbani'),
(400, 34, 'Pilibhit'),
(401, 35, 'Pithoragarh'),
(402, 27, 'Poducherry (PD)'),
(403, 31, 'Poducherry (TN)'),
(404, 31, 'Pollachi'),
(405, 15, 'Poonch'),
(406, 12, 'Porbandar'),
(407, 1, 'Port Blair'),
(408, 2, 'Prakasam'),
(409, 34, 'Pratapgarh'),
(410, 2, 'Proddatur'),
(411, 31, 'Pudukkottai'),
(412, 15, 'Pulwama'),
(413, 21, 'Pune'),
(414, 26, 'Puri'),
(415, 5, 'Purnea'),
(416, 36, 'Purulia'),
(417, 17, 'Puttur'),
(418, 18, 'Quilon'),
(419, 34, 'Raebareli'),
(420, 17, 'Raichur'),
(421, 7, 'Raigarh (CT)'),
(422, 21, 'Raigarh (MH)'),
(423, 7, 'Raipur'),
(424, 2, 'Rajahmundry'),
(425, 15, 'Rajauri'),
(426, 12, 'Rajkot'),
(427, 29, 'Rajsamand'),
(428, 31, 'Ramanathapuram'),
(429, 34, 'Rampur'),
(430, 14, 'Rampur Bushahr'),
(431, 16, 'Ranchi'),
(432, 1, 'Rangat'),
(433, 20, 'Ratlam'),
(434, 21, 'Ratnagiri'),
(435, 15, 'Reasi'),
(436, 20, 'Rewa'),
(437, 23, 'Ri Bhoi'),
(438, 13, 'Rohtak'),
(439, 5, 'Rohtas'),
(440, 28, 'Ropar'),
(441, 35, 'Rudraprayag'),
(442, 28, 'Rupnagar'),
(443, 12, 'Sabarkantha'),
(444, 20, 'Sagar'),
(445, 34, 'Saharanpur'),
(446, 5, 'Saharsa'),
(447, 31, 'Salem East'),
(448, 31, 'Salem West'),
(449, 5, 'Samastipur'),
(450, 26, 'Sambalpur'),
(451, 32, 'Sangareddy'),
(452, 21, 'Sangli'),
(453, 28, 'Sangrur'),
(454, 34, 'Sant Kabir Nagar'),
(455, 34, 'Sant Ravidas Nagar'),
(456, 16, 'Santhal Parganas'),
(457, 5, 'Saran'),
(458, 21, 'Satara'),
(459, 29, 'Sawai Madhopur'),
(460, 32, 'Secunderabad'),
(461, 20, 'Sehore'),
(462, 22, 'Senapati'),
(463, 24, 'Serchhip'),
(464, 20, 'Shahdol'),
(465, 34, 'Shahjahanpur'),
(466, 14, 'Shimla'),
(467, 17, 'Shimoga'),
(468, 34, 'Shrawasti'),
(469, 4, 'Sibsagar'),
(470, 34, 'Siddharthnagar'),
(471, 29, 'Sikar'),
(472, 21, 'Sindhudurg'),
(473, 16, 'Singhbhum'),
(474, 29, 'Sirohi'),
(475, 17, 'Sirsi'),
(476, 5, 'Sitamarhi'),
(477, 34, 'Sitapur'),
(478, 31, 'Sivaganga'),
(479, 5, 'Siwan'),
(480, 14, 'Solan'),
(481, 21, 'Solapur'),
(482, 34, 'Sonbhadra'),
(483, 13, 'Sonepat'),
(484, 4, 'Sonitpur'),
(485, 36, 'South 24 Parganas'),
(486, 36, 'South Dinajpur'),
(487, 23, 'South Garo Hills'),
(488, 30, 'South Sikkim'),
(489, 33, 'South Tripura'),
(490, 2, 'Srikakulam'),
(491, 15, 'Srinagar'),
(492, 31, 'Srirangam'),
(493, 34, 'Sultanpur'),
(494, 26, 'Sundargarh'),
(495, 12, 'Surat'),
(496, 12, 'Surendranagar'),
(497, 32, 'Suryapet'),
(498, 2, 'Tadepalligudem'),
(499, 31, 'Tambaram'),
(500, 22, 'Tamenglong'),
(501, 36, 'Tamluk'),
(502, 28, 'Tarn Taran'),
(503, 3, 'Tawang'),
(504, 35, 'Tehri Garhwal'),
(505, 2, 'Tenali'),
(506, 18, 'Thalassery'),
(507, 21, 'Thane'),
(508, 31, 'Thanjavur'),
(509, 31, 'Theni'),
(510, 22, 'Thoubal'),
(511, 4, 'Tinsukia'),
(512, 3, 'Tirap'),
(513, 31, 'Tiruchirapalli'),
(514, 31, 'Tirunelveli'),
(515, 2, 'Tirupati'),
(516, 31, 'Tirupattur'),
(517, 31, 'Tirupur'),
(518, 18, 'Tirur'),
(519, 18, 'Tiruvalla'),
(520, 31, 'Tiruvannamalai'),
(521, 29, 'Tonk'),
(522, 18, 'Trichur'),
(523, 18, 'Trivandrum North'),
(524, 18, 'Trivandrum South'),
(525, 25, 'Tuensang'),
(526, 17, 'Tumkur'),
(527, 31, 'Tuticorin'),
(528, 29, 'Udaipur'),
(529, 35, 'Udham Singh Nagar'),
(530, 15, 'Udhampur'),
(531, 17, 'Udupi'),
(532, 20, 'Ujjain'),
(533, 22, 'Ukhrul'),
(534, 14, 'Una'),
(535, 34, 'Unnao'),
(536, 3, 'Upper Siang'),
(537, 3, 'Upper Subansiri'),
(538, 35, 'Uttarkashi'),
(539, 18, 'Vadakara'),
(540, 12, 'Vadodara East'),
(541, 12, 'Vadodara West'),
(542, 5, 'Vaishali'),
(543, 12, 'Valsad'),
(544, 34, 'Varanasi'),
(545, 31, 'Vellore'),
(546, 20, 'Vidisha'),
(547, 2, 'Vijayawada'),
(548, 31, 'Virudhunagar'),
(549, 2, 'Visakhapatnam'),
(550, 2, 'Vizianagaram'),
(551, 31, 'Vriddhachalam'),
(552, 32, 'Wanaparthy'),
(553, 32, 'Warangal'),
(554, 21, 'Wardha'),
(555, 21, 'Washim'),
(556, 5, 'West Champaran'),
(557, 23, 'West Garo Hills'),
(558, 3, 'West Kameng'),
(559, 23, 'West Khasi Hills'),
(560, 3, 'West Siang'),
(561, 30, 'West Sikkim'),
(562, 33, 'West Tripura'),
(563, 25, 'Wokha'),
(564, 21, 'Yavatmal'),
(565, 25, 'Zunhebotto');

-- --------------------------------------------------------

--
-- Table structure for table `cms_contents`
--

CREATE TABLE `cms_contents` (
  `id` int(11) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `page_name` varchar(200) NOT NULL,
  `page_title` varchar(200) NOT NULL,
  `content` blob DEFAULT NULL,
  `meta_tags` text DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cms_contents`
--

INSERT INTO `cms_contents` (`id`, `slug`, `page_name`, `page_title`, `content`, `meta_tags`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Home', 'Home', 0x3c703e48656c6c6f20576f726c643c2f703e, 'Hello World', 'Hello World', 'Hello World', 'Hello', '2020-10-27 05:40:44', '2020-10-27 01:13:23');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `slab` int(11) NOT NULL,
  `type` enum('flat','percentage') NOT NULL,
  `value` double(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `role_id`, `scheme_id`, `type_id`, `slab`, `type`, `value`) VALUES
(1, 3, 1, 1, 1, 'flat', 2.00),
(2, 3, 1, 1, 2, 'flat', 2.00),
(3, 3, 1, 1, 3, 'flat', 2.00),
(4, 3, 1, 1, 4, 'flat', 2.00),
(5, 3, 1, 2, 1, 'flat', 3.00),
(6, 3, 1, 2, 2, 'flat', 3.00),
(7, 3, 1, 2, 3, 'flat', 3.00),
(8, 3, 1, 2, 4, 'flat', 3.00),
(9, 3, 3, 1, 1, 'flat', 10.00),
(10, 3, 3, 1, 2, 'flat', 10.00),
(11, 3, 3, 1, 3, 'flat', 10.00),
(12, 3, 3, 1, 4, 'flat', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `customer_details`
--

CREATE TABLE `customer_details` (
  `user_id` int(11) NOT NULL,
  `pancardimage` varchar(200) DEFAULT NULL,
  `aadharcardimage` varchar(200) DEFAULT NULL,
  `cancelledchequeimage` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_details`
--

INSERT INTO `customer_details` (`user_id`, `pancardimage`, `aadharcardimage`, `cancelledchequeimage`) VALUES
(5, '1604907611_Jellyfish.jpg', '1604300321_83b30f1a065cbf872c0c945602b14503.jpg', '1604300321_cancelled-cheque.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `default_permissions`
--

CREATE TABLE `default_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `default_permissions`
--

INSERT INTO `default_permissions` (`id`, `role_id`, `permission_id`) VALUES
(167, 2, 1),
(168, 2, 2),
(169, 2, 3),
(170, 2, 4),
(171, 2, 5),
(172, 2, 6),
(173, 2, 7),
(174, 2, 8),
(175, 2, 9),
(176, 2, 10),
(177, 2, 11),
(178, 2, 19),
(179, 2, 20),
(180, 2, 21),
(181, 2, 23),
(182, 2, 24),
(183, 2, 25),
(184, 2, 27),
(185, 2, 28),
(186, 2, 29);

-- --------------------------------------------------------

--
-- Table structure for table `faq_contents`
--

CREATE TABLE `faq_contents` (
  `id` int(11) NOT NULL,
  `question` blob NOT NULL,
  `answer` blob NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `faq_contents`
--

INSERT INTO `faq_contents` (`id`, `question`, `answer`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0x57686174206973206c6f72656d20697073756d3f, 0x4c6f72656d20497073756d20697320612064756d6d792064657874, '1', '2020-10-22 05:34:21', '2020-11-18 03:40:42', NULL),
(2, 0x57686174206973206c6f72656d20697073756d3f, 0x497420697320612064756d6d7920746578742e2e2e, '1', '2020-10-26 23:19:11', '2020-10-26 23:52:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_slabs`
--

CREATE TABLE `loan_slabs` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `option1` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loan_slabs`
--

INSERT INTO `loan_slabs` (`id`, `name`, `option1`) VALUES
(1, 'Less than equals Rs. 25,000', 'slab1'),
(2, 'Rs.25,001 - Rs.1,00,000', 'slab2'),
(3, 'Rs.1,00,001 - Rs.10,00,000', 'slab3'),
(4, 'Greater than Rs.10,00,000', 'slab4');

-- --------------------------------------------------------

--
-- Table structure for table `loan_types`
--

CREATE TABLE `loan_types` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loan_types`
--

INSERT INTO `loan_types` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Personal Loan', '1', '2020-11-03 04:41:12', '2020-11-17 04:03:02', NULL),
(2, 'Home Loan', '1', '2020-11-03 05:08:53', '2020-11-04 00:02:58', NULL),
(3, 'Car Loan', '1', '2020-11-03 05:10:01', '2020-11-03 05:10:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `original_price` double(10,2) NOT NULL,
  `offered_price` double(10,2) DEFAULT 0.00,
  `description` tinytext DEFAULT NULL,
  `validity` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `featured` enum('1','0') NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `role_id`, `name`, `slug`, `original_price`, `offered_price`, `description`, `validity`, `status`, `featured`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Monthly Plan', 'monthly', 100.00, 49.00, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 30, '1', '1', '2020-11-03 01:01:41', '2020-12-17 23:50:16', NULL),
(3, 4, 'Monthly', 'monthly', 999.00, NULL, 'For testing purpose', 30, '1', '0', '2020-11-03 01:35:02', '2020-11-09 00:19:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `otp` tinytext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `type` varchar(100) NOT NULL,
  `role_id` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `type`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'View FAQs', 'view_faqs', 'cms', '[\"2\"]', '2020-10-22 04:21:40', '2020-10-28 00:30:41'),
(2, 'Add FAQ', 'add_faq', 'cms', '[\"2\"]', '2020-10-22 04:56:38', '2020-10-22 04:56:48'),
(3, 'Edit FAQ', 'edit_faq', 'cms', '[\"2\"]', '2020-10-22 04:57:05', '2020-10-22 04:57:13'),
(4, 'Delete FAQ', 'delete_faq', 'cms', '[\"2\"]', '2020-10-22 04:57:27', '2020-10-22 04:57:27'),
(5, 'View Contents', 'view_contents', 'cms', '[\"2\"]', '2020-10-27 01:19:33', '2020-10-27 01:19:33'),
(6, 'Edit Content', 'edit_content', 'cms', '[\"2\"]', '2020-10-27 01:19:43', '2020-10-27 01:19:43'),
(7, 'View Testimonials', 'view_testimonials', 'cms', '[\"2\"]', '2020-10-27 02:01:22', '2020-10-27 02:01:22'),
(8, 'Add Testimonial', 'add_testimonial', 'cms', '[\"2\"]', '2020-10-27 02:01:38', '2020-10-27 02:01:38'),
(9, 'Edit Testimonial', 'edit_testimonial', 'cms', '[\"2\"]', '2020-10-27 02:01:59', '2020-10-27 02:01:59'),
(10, 'Delete Testimonial', 'delete_testimonial', 'cms', '[\"2\"]', '2020-10-27 02:02:12', '2020-10-27 02:02:12'),
(11, 'View Blogs', 'view_blogs', 'blogs', '[\"2\"]', '2020-10-27 02:10:48', '2020-10-27 02:10:48'),
(12, 'Add Blog', 'add_blog', 'blogs', '[\"2\"]', '2020-10-27 02:11:03', '2020-10-27 02:11:03'),
(13, 'Edit Blog', 'edit_blog', 'blogs', '[\"2\"]', '2020-10-27 02:11:18', '2020-10-27 02:11:18'),
(14, 'Delete Blog', 'delete_blog', 'blogs', '[\"2\"]', '2020-10-27 02:11:32', '2020-10-27 02:11:32'),
(15, 'View Admins', 'view_admins', 'members', '[\"2\"]', '2020-10-28 01:05:44', '2020-10-28 01:05:44'),
(16, 'Add Admin', 'add_admin', 'members', '[\"2\"]', '2020-10-28 01:07:07', '2020-10-28 01:07:07'),
(17, 'Edit Admin', 'edit_admin', 'members', '[\"2\"]', '2020-10-28 01:07:44', '2020-10-28 01:07:44'),
(18, 'Delete Admin', 'delete_admin', 'members', '[\"2\"]', '2020-10-28 01:07:52', '2020-10-28 01:07:52'),
(19, 'View Agents', 'view_agents', 'members', '[\"2\"]', '2020-10-28 01:08:06', '2020-10-28 01:08:06'),
(20, 'Add Agent', 'add_agent', 'members', '[\"2\"]', '2020-10-28 01:08:12', '2020-10-28 01:08:12'),
(21, 'Edit Agent', 'edit_agent', 'members', '[\"2\"]', '2020-10-28 01:08:19', '2020-10-28 01:08:19'),
(22, 'Delete Agent', 'delete_agent', 'members', '[\"2\"]', '2020-10-28 01:08:26', '2020-10-28 01:08:26'),
(23, 'View Banks', 'view_banks', 'members', '[\"2\"]', '2020-10-28 01:08:51', '2020-10-28 01:08:51'),
(24, 'Add Bank', 'add_bank', 'members', '[\"2\"]', '2020-10-28 01:08:58', '2020-10-28 01:08:58'),
(25, 'Edit Bank', 'edit_bank', 'members', '[\"2\"]', '2020-10-28 01:09:05', '2020-10-28 01:09:05'),
(26, 'Delete Bank', 'delete_bank', 'members', '[\"2\"]', '2020-10-28 01:09:11', '2020-10-28 01:09:11'),
(27, 'View Customers', 'view_customers', 'members', '[\"2\"]', '2020-10-28 01:09:19', '2020-10-28 01:09:19'),
(28, 'Add Customer', 'add_customer', 'members', '[\"2\"]', '2020-10-28 01:09:26', '2020-10-28 01:09:54'),
(29, 'Edit Customer', 'edit_customer', 'members', '[\"2\"]', '2020-10-28 01:09:34', '2020-10-28 01:10:04'),
(30, 'Delete Customer', 'delete_customer', 'members', '[\"2\"]', '2020-10-28 01:09:42', '2020-10-28 01:09:42'),
(31, 'Send Account Notification', 'account_notification', 'notification', '[\"2\"]', '2020-10-29 07:43:17', '2020-10-29 07:43:17'),
(32, 'Send SMS Notification', 'sms_notification', 'notification', '[\"2\"]', '2020-10-29 07:43:30', '2020-10-29 07:43:30'),
(33, 'Send Push Notification', 'push_notification', 'notification', '[\"2\"]', '2020-10-29 07:43:40', '2020-10-29 07:43:40'),
(34, 'Send Email Notification', 'email_notification', 'notification', '[\"2\"]', '2020-10-29 07:43:48', '2020-10-29 07:43:48'),
(35, 'View Bank MBRSHP Packages', 'view_bank_membership_packages', 'resources', '[\"2\"]', '2020-11-02 05:00:57', '2020-11-02 07:16:50'),
(36, 'Add Bank MBRSHP Package', 'add_bank_membership_package', 'resources', '[\"2\"]', '2020-11-02 05:01:12', '2020-11-02 07:16:57'),
(37, 'Edit Bank MBRSHP Package', 'edit_bank_membership_package', 'resources', '[\"2\"]', '2020-11-02 05:01:21', '2020-11-02 07:17:18'),
(38, 'View Agent MBRSHP Packages', 'view_agent_membership_packages', 'resources', '[\"2\"]', '2020-11-02 07:19:42', '2020-11-02 07:19:42'),
(39, 'Add Agent MBRSHP Package', 'add_agent_membership_package', 'resources', '[\"2\"]', '2020-11-02 07:19:56', '2020-11-02 07:19:56'),
(40, 'Edit Agent MBRSHP Package', 'edit_agent_membership_package', 'resources', '[\"2\"]', '2020-11-02 07:20:07', '2020-11-02 07:20:07'),
(41, 'View Loan Types', 'view_loantypes', 'setup', '[\"2\"]', '2020-11-03 02:20:23', '2020-11-03 04:05:24'),
(42, 'Add Loan Type', 'add_loantype', 'setup', '[\"2\"]', '2020-11-03 02:20:32', '2020-11-03 04:05:30'),
(43, 'Edit Loan Type', 'edit_loantype', 'setup', '[\"2\"]', '2020-11-03 02:20:47', '2020-11-03 04:05:36'),
(44, 'Delete Loan Type', 'delete_loantype', 'setup', '[\"2\"]', '2020-11-03 02:21:02', '2020-11-03 04:05:42'),
(45, 'View Agent Schemes', 'view_agent_schemes', 'resources', '[\"2\"]', '2020-11-04 01:07:35', '2020-11-04 01:07:35'),
(46, 'Add Agent Scheme', 'add_agent_scheme', 'resources', '[\"2\"]', '2020-11-04 01:07:43', '2020-11-04 01:07:43'),
(47, 'Edit Agent Scheme', 'edit_agent_scheme', 'resources', '[\"2\"]', '2020-11-04 01:07:48', '2020-11-04 01:07:48'),
(48, 'Delete Agent Scheme', 'delete_agent_scheme', 'resources', '[\"2\"]', '2020-11-04 01:08:00', '2020-11-04 01:08:00'),
(49, 'Manage Agent Commission', 'manage_agent_commission', 'resources', '[\"2\"]', '2020-11-04 04:54:47', '2020-11-04 04:54:47');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin', '2020-10-22 04:57:30', '2020-10-22 04:38:39'),
(2, 'Administrative', 'admin', '2020-10-22 04:57:30', '2020-10-22 03:47:03'),
(3, 'Agent', 'agent', '2020-10-22 04:57:30', '2020-10-22 04:57:30'),
(4, 'Bank', 'bank', '2020-10-22 04:57:30', '2020-10-22 04:57:30'),
(5, 'Customer', 'customer', '2020-10-22 04:57:30', '2020-10-22 04:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `schemes`
--

CREATE TABLE `schemes` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schemes`
--

INSERT INTO `schemes` (`id`, `role_id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Regular Package', '1', '2020-11-04 01:28:58', '2020-12-15 00:02:08', NULL),
(2, 3, 'Gold Package', '1', '2020-11-04 01:40:27', '2020-12-15 00:00:29', NULL),
(3, 3, 'Silver Package', '1', '2020-11-04 01:40:34', '2020-12-15 00:00:30', NULL),
(4, 3, 'Platinum Package', '1', '2020-11-04 01:40:43', '2020-11-04 05:28:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `title` tinytext NOT NULL,
  `smsflag` enum('1','0') NOT NULL DEFAULT '1',
  `smssender` varchar(100) DEFAULT NULL,
  `smsuser` varchar(100) DEFAULT NULL,
  `smspwd` varchar(100) DEFAULT NULL,
  `mailhost` tinytext DEFAULT NULL,
  `mailport` tinytext DEFAULT NULL,
  `mailenc` tinytext DEFAULT NULL,
  `mailuser` tinytext DEFAULT NULL,
  `mailpwd` tinytext DEFAULT NULL,
  `mailfrom` tinytext DEFAULT NULL,
  `mailname` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `title`, `smsflag`, `smssender`, `smsuser`, `smspwd`, `mailhost`, `mailport`, `mailenc`, `mailuser`, `mailpwd`, `mailfrom`, `mailname`) VALUES
(1, 'Wealthvise', 'Another Laravel Website', '1', 'TESPOR', 'smsuser', 'smspwd', 'smtp.mailtrap.io', '2525', 'tls', '026e3153f505a3', 'df6ca9db46cd63', 'noreply@wealthvise.com', 'Wealthvise Care');

-- --------------------------------------------------------

--
-- Table structure for table `testimonial_contents`
--

CREATE TABLE `testimonial_contents` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `designation` varchar(200) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `testimonial_contents`
--

INSERT INTO `testimonial_contents` (`id`, `name`, `designation`, `image`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Pramit Paul', 'Web Developer', '1603783011_Koala.jpg', '1', '2020-10-27 01:46:09', '2020-12-04 07:30:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `mobile_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `pancard` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','others') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `mainwallet` double(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `mobile`, `profile_image`, `email_verified_at`, `mobile_verified_at`, `password`, `api_token`, `remember_token`, `status`, `pancard`, `gender`, `dob`, `pincode`, `city_id`, `mainwallet`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin Team', 'pramit.paul@ivanwebsolutions.com', NULL, '1606713301_Koala.jpg', '2020-10-29 03:52:42', '2020-10-29 04:29:12', '$2y$10$H00EHEjY1YNuE98auEqLhOjxrjC2fsfK1faheC9nZZs8rHGMaTF/6', NULL, 'HvrBf7MVGR7pDCFzJIeyYpRyYzWf8LRUWhR3OX8FESbmpBcYVdoWlx8vavdK', '1', NULL, NULL, NULL, NULL, NULL, 0.00, '2020-10-19 10:11:00', '2020-11-29 23:45:01'),
(2, 2, 'Admin Pramit', 'pramit.paul@ivanwebsolutions.co.in', '9876543210', '1603805090_Tulips.jpg', NULL, NULL, '$2y$10$jnqIHH/7sGrG5wyui.lsWe26BCNZQG6g/xL8oGh1alxNanO1WjdQK', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, 0.00, '2020-10-27 06:08:20', '2020-10-27 23:16:41'),
(3, 2, 'hello.ivaninfotech.com', 'hello@ivaninfotech.com', NULL, NULL, NULL, NULL, '$2y$10$qm4dNhqMRM265LdjGtm/K.KDa92FLcNH3Am0KbHA4mcA2Uywtk3Oa', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, 0.00, '2020-10-28 01:48:48', '2020-10-28 01:48:48'),
(4, 2, 'myadmin@gmail.com', 'myadmin@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$ulJjHIvNDN4Pqk9MI7y81.xEVlzkNzItdFVF/.Nf46JbEwMVdqVDu', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, 0.00, '2020-10-28 01:49:32', '2020-10-28 01:49:32'),
(5, 5, 'Pramit Paul', 'hello@ivan.com', '7894561230', '1604061234_Tulips.jpg', NULL, '2020-10-29 05:40:33', '$2y$10$50PmajPMoX6w9TpkrT6/t.33X972F6qMza.zJ.F1rtWDZhADg4E4a', 'SwebDsquO3M6B8429m48FTeZwZKpuZGBINyTRolzPt9P4UDzrkDSkerGGmzT', NULL, '1', 'ABCDE1234H', 'male', '2002-11-03', 713101, 90, 0.00, '2020-10-29 01:13:23', '2021-01-27 01:27:07'),
(6, 5, 'John Doe', 'jon@hll.com', '7899877889', '1603958934_Hydrangeas.jpg', '2020-10-29 03:49:36', '2020-10-29 02:38:15', '$2y$10$QfcVpoIVcWrwfk./aS7/OeDQJ1p6.n5oa6G04pu/P99A2Je23Hbtm', NULL, NULL, '1', 'ABCDE1234G', NULL, NULL, NULL, NULL, 0.00, '2020-10-29 01:50:45', '2020-11-02 03:41:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `type` enum('account','email','push','sms') NOT NULL DEFAULT 'account',
  `heading` tinyblob NOT NULL,
  `body` tinyblob NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `type`, `heading`, `body`, `created_at`, `updated_at`) VALUES
(1, '[\"5\"]', 'account', 0x68656c6c6f, 0x74657374, '2020-10-30 02:10:54', '2020-10-30 02:10:54'),
(2, 'null', 'account', 0x313233, 0x313233, '2020-10-30 02:27:16', '2020-10-30 02:27:16'),
(3, '[\"6\"]', 'push', 0x73, 0x73, '2020-10-30 06:34:31', '2020-10-30 06:34:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`) VALUES
(1, 2, 27),
(2, 2, 28),
(3, 2, 29),
(4, 2, 1),
(5, 2, 2),
(6, 2, 3),
(7, 2, 5),
(8, 2, 6),
(9, 2, 7),
(10, 2, 8),
(11, 2, 9),
(12, 2, 11),
(13, 2, 12),
(14, 2, 13),
(15, 4, 19),
(16, 4, 20),
(17, 4, 21),
(18, 4, 23),
(19, 4, 24),
(20, 4, 25),
(21, 4, 27),
(22, 4, 28),
(23, 4, 29),
(24, 4, 1),
(25, 4, 2),
(26, 4, 3),
(27, 4, 4),
(28, 4, 5),
(29, 4, 6),
(30, 4, 7),
(31, 4, 8),
(32, 4, 9),
(33, 4, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_contents`
--
ALTER TABLE `cms_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_details`
--
ALTER TABLE `customer_details`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `default_permissions`
--
ALTER TABLE `default_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_contents`
--
ALTER TABLE `faq_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_slabs`
--
ALTER TABLE `loan_slabs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_types`
--
ALTER TABLE `loan_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `schemes`
--
ALTER TABLE `schemes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonial_contents`
--
ALTER TABLE `testimonial_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `api_token` (`api_token`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;

--
-- AUTO_INCREMENT for table `cms_contents`
--
ALTER TABLE `cms_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `default_permissions`
--
ALTER TABLE `default_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `faq_contents`
--
ALTER TABLE `faq_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loan_slabs`
--
ALTER TABLE `loan_slabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loan_types`
--
ALTER TABLE `loan_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `schemes`
--
ALTER TABLE `schemes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testimonial_contents`
--
ALTER TABLE `testimonial_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
