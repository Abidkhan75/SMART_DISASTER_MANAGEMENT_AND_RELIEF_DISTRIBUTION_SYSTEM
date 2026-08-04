CREATE TABLE ZONE(
 Zone_ID VARCHAR(5) PRIMARY KEY,
 City VARCHAR(50),
 District VARCHAR(50),
 Division VARCHAR(50),
 Population INT,
 Risk_Level VARCHAR(20)
);

CREATE TABLE DISASTER(
 Disaster_ID VARCHAR(5) PRIMARY KEY,
 Disaster_Name VARCHAR(100),
 Severity_Level VARCHAR(20),
 Start_Time DATE,
 End_Time DATE,
 Status VARCHAR(20)
);

CREATE TABLE SHELTER(
 Shelter_ID VARCHAR(5) PRIMARY KEY,
 Zone_ID VARCHAR(5),
 Capacity INT,
 Current_Occupancy INT,
 Address VARCHAR(100),
 Contact_No VARCHAR(15),
 Status VARCHAR(20),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID)
);

CREATE TABLE ORGANIZATION(
 Organization_ID VARCHAR(5) PRIMARY KEY,
 Organization_Name VARCHAR(100),
 Address VARCHAR(100),
 Contact_No VARCHAR(15),
 Email VARCHAR(100)
);

CREATE TABLE RESOURCE(
 Resource_ID VARCHAR(5) PRIMARY KEY,
 Resource_Name VARCHAR(100),
 Category VARCHAR(50),
 Unit VARCHAR(30),
 Unit_Cost DECIMAL(10,2)
);

CREATE TABLE VICTIM(
 Victim_ID VARCHAR(5) PRIMARY KEY,
 NID BIGINT UNIQUE,
 Shelter_ID VARCHAR(5),
 Zone_ID VARCHAR(5),
 Full_Name VARCHAR(100),
 Age INT,
 Gender VARCHAR(10),
 Family_Size INT,
 Medical_Status VARCHAR(20),
 Contact_No VARCHAR(15),
 FOREIGN KEY (Shelter_ID) REFERENCES SHELTER(Shelter_ID),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID)
);

CREATE TABLE DISASTER_ZONE(
 DisasterZone_ID VARCHAR(6) PRIMARY KEY,
 Disaster_ID VARCHAR(5),
 Zone_ID VARCHAR(5),
 Affected_Population INT,
 Estimated_Budget DECIMAL(12,2),
 Relief_Status VARCHAR(20),
 Damage_Level VARCHAR(20),
 FOREIGN KEY (Disaster_ID) REFERENCES DISASTER(Disaster_ID),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID)
);

CREATE TABLE VOLUNTEER(
 Volunteer_ID VARCHAR(6) PRIMARY KEY,
 Organization_ID VARCHAR(5),
 Zone_ID VARCHAR(5),
 Full_Name VARCHAR(100),
 Phone VARCHAR(15),
 Gender VARCHAR(10),
 Skill VARCHAR(50),
 Availability VARCHAR(20),
 FOREIGN KEY (Organization_ID) REFERENCES ORGANIZATION(Organization_ID),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID)
);

CREATE TABLE INVENTORY(
 Inventory_ID VARCHAR(5) PRIMARY KEY,
 Shelter_ID VARCHAR(5),
 Resource_ID VARCHAR(5),
 Organization_ID VARCHAR(5),
 Zone_ID VARCHAR(5),
 Quantity INT,
 Last_Updated DATE,
 FOREIGN KEY (Shelter_ID) REFERENCES SHELTER(Shelter_ID),
 FOREIGN KEY (Resource_ID) REFERENCES RESOURCE(Resource_ID),
 FOREIGN KEY (Organization_ID) REFERENCES ORGANIZATION(Organization_ID),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID)
);

CREATE TABLE RELIEF_DISTRIBUTION(
 Dis_ID VARCHAR(5) PRIMARY KEY,
 Victim_ID VARCHAR(5),
 Zone_ID VARCHAR(5),
 Volunteer_ID VARCHAR(6),
 Organization_ID VARCHAR(5),
 Resource_ID VARCHAR(5),
 Quantity INT,
 Dis_Date DATE,
 Dis_Status VARCHAR(20),
 FOREIGN KEY (Victim_ID) REFERENCES VICTIM(Victim_ID),
 FOREIGN KEY (Zone_ID) REFERENCES ZONE(Zone_ID),
 FOREIGN KEY (Volunteer_ID) REFERENCES VOLUNTEER(Volunteer_ID),
 FOREIGN KEY (Organization_ID) REFERENCES ORGANIZATION(Organization_ID),
 FOREIGN KEY (Resource_ID) REFERENCES RESOURCE(Resource_ID)
);

INSERT INTO ZONE VALUES
('Z001','Dhaka','Dhaka','Dhaka',1500000,'High'),
('Z002','Dhaka','Dhaka','Dhaka',1200000,'Medium'),
('Z003','Dhaka','Dhaka','Dhaka',950000,'High'),
('Z004','Dhaka','Dhaka','Dhaka',1100000,'Medium'),
('Z005','Dhaka','Dhaka','Dhaka',850000,'Low');

INSERT INTO DISASTER VALUES
('D001','Urban Flood','High','2026-07-10','2026-07-15','Completed'),
('D002','Fire Accident','Medium','2026-05-20','2026-05-20','Completed'),
('D003','Building Collapse','High','2026-06-01','2026-06-03','Completed'),
('D004','Waterlogging','Medium','2026-07-18','2026-07-20','Ongoing'),
('D005','Gas Explosion','High','2026-04-12','2026-04-12','Completed');

INSERT INTO SHELTER VALUES
('S001','Z001',500,320,'Mirpur Govt School','01711111111','Active'),
('S002','Z002',700,500,'Mohammadpur College','01722222222','Active'),
('S003','Z003',450,220,'Dhanmondi Community Center','01733333333','Active'),
('S004','Z004',650,610,'Uttara High School','01744444444','Full'),
('S005','Z005',400,150,'Badda Govt School','01755555555','Active');

INSERT INTO ORGANIZATION VALUES
('O001','Bangladesh Red Crescent Society','Dhaka','01811111111','info@bdrcs.org'),
('O002','BRAC','Mohakhali','01822222222','help@brac.net'),
('O003','Dhaka North City Corporation','Gulshan','01833333333','support@dncc.gov.bd'),
('O004','Bangladesh Army','Cantonment','01844444444','army@mil.bd'),
('O005','Fire Service & Civil Defence','Siddiqbazar','01855555555','rescue@fireservice.gov.bd');

INSERT INTO RESOURCE VALUES
('R001','Rice','Food','Kg',75),
('R002','Drinking Water','Water','Bottle',30),
('R003','Blanket','Clothing','Piece',650),
('R004','Medicine Kit','Medical','Box',1200),
('R005','Baby Food','Food','Packet',450);

INSERT INTO VICTIM VALUES
('V001',1990123456789,'S001','Z001','Md. Rahim Uddin',35,'Male',5,'Stable','01911111111'),
('V002',1991456789123,'S002','Z002','Nusrat Jahan',29,'Female',4,'Injured','01922222222'),
('V003',1989456123789,'S003','Z003','Mohammad Karim',47,'Male',6,'Stable','01933333333'),
('V004',2001123456780,'S004','Z004','Sadia Akter',24,'Female',3,'Critical','01944444444'),
('V005',1993567891234,'S005','Z005','Rakib Hasan',31,'Male',5,'Stable','01955555555');

INSERT INTO DISASTER_ZONE VALUES
('DZ001','D001','Z001',20000,5000000,'Ongoing','Severe'),
('DZ002','D002','Z002',8000,1800000,'Completed','Medium'),
('DZ003','D003','Z003',12000,7000000,'Ongoing','Severe'),
('DZ004','D004','Z004',15000,3500000,'Ongoing','Medium'),
('DZ005','D005','Z005',6000,2500000,'Completed','High');

INSERT INTO VOLUNTEER VALUES
('VL001','O001','Z001','Tanvir Ahmed','01611111111','Male','First Aid','Available'),
('VL002','O002','Z002','Jannatul Ferdous','01622222222','Female','Food Distribution','Available'),
('VL003','O003','Z003','Ashraful Islam','01633333333','Male','Rescue','Busy'),
('VL004','O004','Z004','Farhana Yasmin','01644444444','Female','Medical Support','Available'),
('VL005','O005','Z005','Imran Hossain','01655555555','Male','Fire Rescue','Available');

INSERT INTO INVENTORY VALUES
('I001','S001','R001','O001','Z001',500,'2026-07-12'),
('I002','S002','R002','O002','Z002',1000,'2026-07-12'),
('I003','S003','R003','O003','Z003',300,'2026-07-13'),
('I004','S004','R004','O004','Z004',200,'2026-07-14'),
('I005','S005','R005','O005','Z005',400,'2026-07-15');

INSERT INTO RELIEF_DISTRIBUTION VALUES
('RD001','V001','Z001','VL001','O001','R001',20,'2026-07-12','Delivered'),
('RD002','V002','Z002','VL002','O002','R002',50,'2026-07-12','Delivered'),
('RD003','V003','Z003','VL003','O003','R003',10,'2026-07-13','Pending'),
('RD004','V004','Z004','VL004','O004','R004',5,'2026-07-14','Delivered'),
('RD005','V005','Z005','VL005','O005','R005',15,'2026-07-15','Delivered');
