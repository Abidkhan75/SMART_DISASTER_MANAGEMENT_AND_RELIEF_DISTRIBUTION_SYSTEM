-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2026 at 07:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `disastermanagementdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` varchar(10) NOT NULL,
  `Admin_Name` varchar(25) NOT NULL,
  `Admin_Number` varchar(25) NOT NULL,
  `Admin_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Admin_Name`, `Admin_Number`, `Admin_Password`) VALUES
('A001', 'Ad001', 'Sohag Mia', '01771154686'),
('A002', 'Ad001', 'Jashim Hossain', '01537834892');

-- --------------------------------------------------------

--
-- Table structure for table `disaster`
--

CREATE TABLE `disaster` (
  `Disaster_ID` varchar(5) NOT NULL,
  `Disaster_Name` varchar(100) DEFAULT NULL,
  `Severity_Level` varchar(20) DEFAULT NULL,
  `Start_Time` date DEFAULT NULL,
  `End_Time` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disaster`
--

INSERT INTO `disaster` (`Disaster_ID`, `Disaster_Name`, `Severity_Level`, `Start_Time`, `End_Time`, `Status`) VALUES
('D001', 'Sylhet Flood 2026', 'High', '2026-06-10', '2026-06-25', 'Resolved'),
('D002', 'Cyclone Remal', 'Critical', '2026-05-18', '2026-05-30', 'Resolved'),
('D003', 'Chattogram Landslide', 'Medium', '2026-07-04', '2026-07-08', 'Resolved'),
('D004', 'Dhaka Urban Fire', 'Medium', '2026-01-15', '2026-01-16', 'Resolved'),
('D005', 'Sunamganj Flash Flood', 'Critical', '2026-04-21', '2026-04-28', 'Resolved'),
('D006', 'Khulna River Erosion', 'High', '2026-03-12', '2026-04-02', 'Resolved'),
('D007', 'Rajshahi Heatwave', 'Medium', '2024-05-01', '2024-05-09', 'Resolved'),
('D008', 'Barisal Cyclonic Storm', 'High', '2026-08-11', '2026-08-16', 'Resolved'),
('D009', 'Rangpur Cold Wave', 'Low', '2026-12-18', '2026-12-28', 'Resolved'),
('D010', 'Cumilla Tornado', 'High', '2026-09-06', '2026-09-07', 'Resolved'),
('D011', 'Feni Flood', 'High', '2026-07-18', '2026-07-28', 'Resolved'),
('D012', 'Cox\'s Bazar Sea Surge', 'Critical', '2026-10-10', '2026-10-15', 'Resolved'),
('D013', 'Bandarban Landslide', 'High', '2026-06-02', '2026-06-09', 'Resolved'),
('D014', 'Gazipur Factory Fire', 'Critical', '2026-02-11', '2026-02-12', 'Resolved'),
('D015', 'Pabna River Flood', 'Medium', '2026-08-20', '2026-08-30', 'Resolved'),
('D016', 'Noakhali Cyclone', 'Critical', '2026-11-04', '2026-11-10', 'Resolved'),
('D017', 'Brahmanbaria Flood', 'High', '2026-06-09', '2026-07-24', 'Active'),
('D018', 'Mymensingh River Erosion', 'Medium', '2026-03-21', '2026-04-06', 'Resolved'),
('D019', 'Dhaka Earth Tremor', 'Low', '2026-09-15', '2026-09-15', 'Resolved'),
('D020', 'Satkhira Cyclone', 'Critical', '2026-10-22', '2026-10-29', 'Active'),
('D021', 'Cyclon', 'Medium', '2026-07-05', '2026-07-20', 'Resolved'),
('D022', 'Dengu', 'Critical', '2026-06-15', '0000-00-00', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `disaster_zone`
--

CREATE TABLE `disaster_zone` (
  `DisasterZone_ID` varchar(6) NOT NULL,
  `Disaster_ID` varchar(5) DEFAULT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Affected_Population` int(11) DEFAULT NULL,
  `Estimated_Budget` decimal(12,2) DEFAULT NULL,
  `Relief_Status` varchar(20) DEFAULT NULL,
  `Damage_Level` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disaster_zone`
--

INSERT INTO `disaster_zone` (`DisasterZone_ID`, `Disaster_ID`, `Zone_ID`, `Affected_Population`, `Estimated_Budget`, `Relief_Status`, `Damage_Level`) VALUES
('DZ001', 'D001', 'Z001', 20000, 5000000.00, 'Ongoing', 'Severe'),
('DZ002', 'D002', 'Z002', 8000, 1800000.00, 'Completed', 'Medium'),
('DZ003', 'D003', 'Z003', 12000, 7000000.00, 'Ongoing', 'Severe'),
('DZ004', 'D004', 'Z004', 15000, 3500000.00, 'Ongoing', 'Medium'),
('DZ005', 'D005', 'Z005', 6000, 2500000.00, 'Completed', 'High');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Inventory_ID` varchar(5) NOT NULL,
  `Shelter_ID` varchar(5) DEFAULT NULL,
  `Resource_ID` varchar(5) DEFAULT NULL,
  `Organization_ID` varchar(5) DEFAULT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Last_Updated` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Inventory_ID`, `Shelter_ID`, `Resource_ID`, `Organization_ID`, `Zone_ID`, `Quantity`, `Last_Updated`) VALUES
('I001', 'S001', 'R001', 'O001', 'Z001', 500, '2026-07-12'),
('I002', 'S002', 'R002', 'O002', 'Z002', 1000, '2026-07-12'),
('I003', 'S003', 'R003', 'O003', 'Z003', 300, '2026-07-13'),
('I004', 'S004', 'R004', 'O004', 'Z004', 200, '2026-07-14'),
('I005', 'S005', 'R005', 'O005', 'Z005', 400, '2026-07-15');

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_last_updated` BEFORE UPDATE ON `inventory` FOR EACH ROW BEGIN

    SET NEW.Last_Updated = CURRENT_TIMESTAMP;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `Organization_ID` varchar(5) NOT NULL,
  `Organization_Name` varchar(100) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `Contact_No` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Org_Password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`Organization_ID`, `Organization_Name`, `Address`, `Contact_No`, `Email`, `Org_Password`) VALUES
('O001', 'Bangladesh Red Crescent Society', 'Dhaka', '01811111111', 'info@bdrcs.org', 'Or001'),
('O002', 'BRAC', 'Mohakhali', '01822222222', 'help@brac.net', 'Or001'),
('O003', 'Dhaka North City Corporation', 'Gulshan', '01833333333', 'support@dncc.gov.bd', 'Or001'),
('O004', 'Bangladesh Army', 'Cantonment', '01844444444', 'army@mil.bd', 'Or001'),
('O005', 'Fire Service & Civil Defence', 'Siddiqbazar', '01855555555', 'rescue@fireservice.gov.bd', 'Or001');

-- --------------------------------------------------------

--
-- Table structure for table `relief_distribution`
--

CREATE TABLE `relief_distribution` (
  `Dis_ID` varchar(5) NOT NULL,
  `Victim_ID` varchar(5) DEFAULT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Volunteer_ID` varchar(6) DEFAULT NULL,
  `Organization_ID` varchar(5) DEFAULT NULL,
  `Resource_ID` varchar(5) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Dis_Date` date DEFAULT NULL,
  `Dis_Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relief_distribution`
--

INSERT INTO `relief_distribution` (`Dis_ID`, `Victim_ID`, `Zone_ID`, `Volunteer_ID`, `Organization_ID`, `Resource_ID`, `Quantity`, `Dis_Date`, `Dis_Status`) VALUES
('RD001', 'V001', 'Z001', 'VL001', 'O001', 'R001', 20, '2026-07-12', 'Delivered'),
('RD002', 'V002', 'Z002', 'VL002', 'O002', 'R002', 50, '2026-07-12', 'Delivered'),
('RD003', 'V003', 'Z003', 'VL003', 'O003', 'R003', 10, '2026-07-13', 'Pending'),
('RD004', 'V004', 'Z004', 'VL004', 'O004', 'R004', 5, '2026-07-14', 'Delivered'),
('RD005', 'V005', 'Z005', 'VL005', 'O005', 'R005', 15, '2026-07-15', 'Delivered');

--
-- Triggers `relief_distribution`
--
DELIMITER $$
CREATE TRIGGER `trg_prevent_insufficient_inventory` BEFORE INSERT ON `relief_distribution` FOR EACH ROW BEGIN

    DECLARE available_quantity INT;

    SELECT Quantity
    INTO available_quantity
    FROM inventory
    WHERE Resource_ID = NEW.Resource_ID
      AND Organization_ID = NEW.Organization_ID;

    IF available_quantity IS NULL THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Inventory record not found.';

    END IF;

    IF available_quantity < NEW.Quantity THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient inventory quantity.';

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_reduce_inventory` AFTER INSERT ON `relief_distribution` FOR EACH ROW BEGIN

    UPDATE inventory
    SET Quantity = Quantity - NEW.Quantity
    WHERE Resource_ID = NEW.Resource_ID
      AND Organization_ID = NEW.Organization_ID;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `Resource_ID` varchar(5) NOT NULL,
  `Resource_Name` varchar(100) DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Unit` varchar(30) DEFAULT NULL,
  `Unit_Cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`Resource_ID`, `Resource_Name`, `Category`, `Unit`, `Unit_Cost`) VALUES
('R001', 'Rice', 'Food', 'Kg', 75.00),
('R002', 'Drinking Water', 'Water', 'Bottle', 30.00),
('R003', 'Blanket', 'Clothing', 'Piece', 650.00),
('R004', 'Medicine Kit', 'Medical', 'Box', 1200.00),
('R005', 'Baby Food', 'Food', 'Packet', 450.00);

-- --------------------------------------------------------

--
-- Table structure for table `shelter`
--

CREATE TABLE `shelter` (
  `Shelter_ID` varchar(5) NOT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `Current_Occupancy` int(11) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `Contact_No` varchar(15) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelter`
--

INSERT INTO `shelter` (`Shelter_ID`, `Zone_ID`, `Capacity`, `Current_Occupancy`, `Address`, `Contact_No`, `Status`) VALUES
('S001', 'Z001', 500, 320, 'Mirpur Govt School', '01711111111', 'Active'),
('S002', 'Z002', 700, 500, 'Mohammadpur College', '01722222222', 'Active'),
('S003', 'Z003', 450, 220, 'Dhanmondi Community Center', '01733333333', 'Active'),
('S004', 'Z004', 650, 610, 'Uttara High School', '01744444444', 'Full'),
('S005', 'Z005', 400, 150, 'Badda Govt School', '01755555555', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `victim`
--

CREATE TABLE `victim` (
  `Victim_ID` varchar(5) NOT NULL,
  `NID` bigint(20) DEFAULT NULL,
  `Shelter_ID` varchar(5) DEFAULT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Full_Name` varchar(100) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Family_Size` int(11) DEFAULT NULL,
  `Medical_Status` varchar(20) DEFAULT NULL,
  `Contact_No` varchar(15) DEFAULT NULL,
  `Victim_Password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `victim`
--

INSERT INTO `victim` (`Victim_ID`, `NID`, `Shelter_ID`, `Zone_ID`, `Full_Name`, `Age`, `Gender`, `Family_Size`, `Medical_Status`, `Contact_No`, `Victim_Password`) VALUES
('V001', 1990123456789, 'S001', 'Z001', 'Md. Rahim Uddin', 35, 'Male', 5, 'Stable', '01911111111', 'Vi001'),
('V002', 1991456789123, 'S002', 'Z002', 'Nusrat Jahan', 29, 'Female', 4, 'Injured', '01922222222', 'Vi001'),
('V003', 1989456123789, 'S003', 'Z003', 'Mohammad Karim', 47, 'Male', 6, 'Stable', '01933333333', 'Vi001'),
('V004', 2001123456780, 'S004', 'Z004', 'Sadia Akter', 24, 'Female', 3, 'Critical', '01944444444', 'Vi001'),
('V005', 1993567891234, 'S005', 'Z005', 'Rakib Hasan', 31, 'Male', 5, 'Stable', '01955555555', 'Vi001');

--
-- Triggers `victim`
--
DELIMITER $$
CREATE TRIGGER `trg_update_shelter_occupancy` AFTER UPDATE ON `victim` FOR EACH ROW BEGIN

    IF OLD.Shelter_ID IS NOT NULL
       AND NEW.Shelter_ID <> OLD.Shelter_ID THEN

        UPDATE shelter
        SET Current_Occupancy = Current_Occupancy - 1
        WHERE Shelter_ID = OLD.Shelter_ID;

        UPDATE shelter
        SET Current_Occupancy = Current_Occupancy + 1
        WHERE Shelter_ID = NEW.Shelter_ID;

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `Volunteer_ID` varchar(6) NOT NULL,
  `Organization_ID` varchar(5) DEFAULT NULL,
  `Zone_ID` varchar(5) DEFAULT NULL,
  `Full_Name` varchar(100) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Skill` varchar(50) DEFAULT NULL,
  `Availability` varchar(20) DEFAULT NULL,
  `Volunteer_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`Volunteer_ID`, `Organization_ID`, `Zone_ID`, `Full_Name`, `Phone`, `Gender`, `Skill`, `Availability`, `Volunteer_Password`) VALUES
('VL001', 'O001', 'Z001', 'Tanvir Ahmed', '01611111111', 'Male', 'First Aid', 'Available', 'Vo001'),
('VL002', 'O002', 'Z002', 'Jannatul Ferdous', '01622222222', 'Female', 'Food Distribution', 'Available', 'Vo001'),
('VL003', 'O003', 'Z003', 'Ashraful Islam', '01633333333', 'Male', 'Rescue', 'Busy', 'Vo001'),
('VL004', 'O004', 'Z004', 'Farhana Yasmin', '01644444444', 'Female', 'Medical Support', 'Available', 'Vo001'),
('VL005', 'O005', 'Z005', 'Imran Hossain', '01655555555', 'Male', 'Fire Rescue', 'Available', 'Vo001');

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `Zone_ID` varchar(5) NOT NULL,
  `City` varchar(50) DEFAULT NULL,
  `District` varchar(50) DEFAULT NULL,
  `Division` varchar(50) DEFAULT NULL,
  `Population` int(11) DEFAULT NULL,
  `Risk_Level` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`Zone_ID`, `City`, `District`, `Division`, `Population`, `Risk_Level`) VALUES
('Z001', 'Bashundhara', 'Dhaka', 'Dhaka', 18000, 'Medium'),
('Z002', 'Banani', 'Dhaka', 'Dhaka', 16000, 'Low'),
('Z003', 'Mirpur', 'Dhaka', 'Dhaka', 26000, 'High'),
('Z004', 'Jatrabari', 'Dhaka', 'Dhaka', 2000, 'High'),
('Z005', 'Uttara Sector 7', 'Dhaka', 'Dhaka', 17000, 'Low'),
('Z006', 'Sylhet Sadar', 'Sylhet', 'Sylhet', 28000, 'High'),
('Z007', 'Sunamganj Sadar', 'Sunamganj', 'Sylhet', 23000, 'Critical'),
('Z008', 'Cox\'s Bazar Sadar', 'Cox\'s Bazar', 'Chattogram', 25000, 'High'),
('Z009', 'Bandarban Sadar', 'Bandarban', 'Chattogram', 12000, 'High'),
('Z010', 'Chattogram EPZ', 'Chattogram', 'Chattogram', 20000, 'Medium'),
('Z011', 'Khulna Sadar', 'Khulna', 'Khulna', 22000, 'Medium'),
('Z012', 'Satkhira Sadar', 'Satkhira', 'Khulna', 18000, 'High'),
('Z013', 'Barisal Sadar', 'Barisal', 'Barisal', 24000, 'High'),
('Z014', 'Rajshahi Sadar', 'Rajshahi', 'Rajshahi', 21000, 'Medium'),
('Z015', 'Rangpur Sadar', 'Rangpur', 'Rangpur', 20000, 'Low'),
('Z016', 'Cumilla Sadar', 'Cumilla', 'Chattogram', 26000, 'Medium'),
('Z017', 'Feni Sadar', 'Feni', 'Chattogram', 19000, 'High'),
('Z018', 'Gazipur Sadar', 'Gazipur', 'Dhaka', 30000, 'Medium'),
('Z019', 'Noakhali Sadar', 'Noakhali', 'Chattogram', 21000, 'High'),
('Z020', 'Mymensingh Sadar', 'Mymensingh', 'Mymensingh', 24000, 'Medium');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `disaster`
--
ALTER TABLE `disaster`
  ADD PRIMARY KEY (`Disaster_ID`);

--
-- Indexes for table `disaster_zone`
--
ALTER TABLE `disaster_zone`
  ADD PRIMARY KEY (`DisasterZone_ID`),
  ADD KEY `Disaster_ID` (`Disaster_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`Inventory_ID`),
  ADD KEY `Shelter_ID` (`Shelter_ID`),
  ADD KEY `Resource_ID` (`Resource_ID`),
  ADD KEY `Organization_ID` (`Organization_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`Organization_ID`);

--
-- Indexes for table `relief_distribution`
--
ALTER TABLE `relief_distribution`
  ADD PRIMARY KEY (`Dis_ID`),
  ADD KEY `Victim_ID` (`Victim_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`),
  ADD KEY `Volunteer_ID` (`Volunteer_ID`),
  ADD KEY `Organization_ID` (`Organization_ID`),
  ADD KEY `Resource_ID` (`Resource_ID`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`Resource_ID`);

--
-- Indexes for table `shelter`
--
ALTER TABLE `shelter`
  ADD PRIMARY KEY (`Shelter_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`);

--
-- Indexes for table `victim`
--
ALTER TABLE `victim`
  ADD PRIMARY KEY (`Victim_ID`),
  ADD UNIQUE KEY `NID` (`NID`),
  ADD KEY `Shelter_ID` (`Shelter_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`Volunteer_ID`),
  ADD KEY `Organization_ID` (`Organization_ID`),
  ADD KEY `Zone_ID` (`Zone_ID`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`Zone_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `disaster_zone`
--
ALTER TABLE `disaster_zone`
  ADD CONSTRAINT `disaster_zone_ibfk_1` FOREIGN KEY (`Disaster_ID`) REFERENCES `disaster` (`Disaster_ID`),
  ADD CONSTRAINT `disaster_zone_ibfk_2` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`Shelter_ID`) REFERENCES `shelter` (`Shelter_ID`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`Resource_ID`) REFERENCES `resource` (`Resource_ID`),
  ADD CONSTRAINT `inventory_ibfk_3` FOREIGN KEY (`Organization_ID`) REFERENCES `organization` (`Organization_ID`),
  ADD CONSTRAINT `inventory_ibfk_4` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`);

--
-- Constraints for table `relief_distribution`
--
ALTER TABLE `relief_distribution`
  ADD CONSTRAINT `relief_distribution_ibfk_1` FOREIGN KEY (`Victim_ID`) REFERENCES `victim` (`Victim_ID`),
  ADD CONSTRAINT `relief_distribution_ibfk_2` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`),
  ADD CONSTRAINT `relief_distribution_ibfk_3` FOREIGN KEY (`Volunteer_ID`) REFERENCES `volunteer` (`Volunteer_ID`),
  ADD CONSTRAINT `relief_distribution_ibfk_4` FOREIGN KEY (`Organization_ID`) REFERENCES `organization` (`Organization_ID`),
  ADD CONSTRAINT `relief_distribution_ibfk_5` FOREIGN KEY (`Resource_ID`) REFERENCES `resource` (`Resource_ID`);

--
-- Constraints for table `shelter`
--
ALTER TABLE `shelter`
  ADD CONSTRAINT `shelter_ibfk_1` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`);

--
-- Constraints for table `victim`
--
ALTER TABLE `victim`
  ADD CONSTRAINT `victim_ibfk_1` FOREIGN KEY (`Shelter_ID`) REFERENCES `shelter` (`Shelter_ID`),
  ADD CONSTRAINT `victim_ibfk_2` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`);

--
-- Constraints for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`Organization_ID`) REFERENCES `organization` (`Organization_ID`),
  ADD CONSTRAINT `volunteer_ibfk_2` FOREIGN KEY (`Zone_ID`) REFERENCES `zone` (`Zone_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
