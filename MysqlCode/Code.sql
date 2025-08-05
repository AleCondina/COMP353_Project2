



#made from what can be seen in table
CREATE TABLE Team (
teamID INT,
name  VARCHAR(255),
gender VARCHAR(255)
);

CREATE TABLE InTeam(
	clubMemberID INT UNIQUE PRIMARY KEY,
    teamID INT,
    FOREIGN KEY(clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY(teamID) REFERENCES Team(teamID)
);


CREATE TABLE PostalInfo(
PostalCode VARCHAR(10) PRIMARY KEY, 
Province VARCHAR(100), 
City VARCHAR(100) 
);

CREATE TABLE TeamMatchUp (
    formationID INT AUTO_INCREMENT PRIMARY KEY,
    team1ID INT NOT NULL,
    team2ID INT NOT NULL,
    locationID INT NOT NULL,
    date DATE NOT NULL,
    startTime TIME NOT NULL,
    sessionType ENUM('Training', 'Game') NOT NULL,
    team1Score INT DEFAULT 0,
    team2Score INT DEFAULT 0,
    FOREIGN KEY (team1ID) REFERENCES Team(teamID),
    FOREIGN KEY (team2ID) REFERENCES Team(teamID),
    FOREIGN KEY (locationID) REFERENCES Location(locationID),
    CHECK (team1ID <> team2ID)
);



CREATE TABLE RoleIn (
    clubMemberID INT,
    formationID INT NOT NULL,
    role ENUM('setter', 'outside hitter', 'opposite hitter', 'libero', 'middle blocker', 'defensive specialist') NOT NULL,
    PRIMARY KEY (clubMemberID),
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY (formationID) REFERENCES TeamMatchUp(formationID)
);
 


CREATE TABLE Location (
    locationID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('Head', 'Branch') NOT NULL,
    address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(50),
    postalCode VARCHAR(10),
    webAddress VARCHAR(100),
    maxCapacity INT
);

CREATE TABLE Personnel (
    personnelID INT AUTO_INCREMENT PRIMARY KEY,
    SSN CHAR(11) UNIQUE NOT NULL, 
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    dateOfBirth DATE,
    medicareNumber CHAR(12) UNIQUE NOT NULL,
    phoneNumber VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(50),
    postalCode VARCHAR(10),
    email VARCHAR(100),
    role ENUM('Administrator', 'Captain', 'Coach', 'Assistant Coach','Head Coach','General Manager', 'Other') NOT NULL,
    mandate ENUM('Volunteer', 'Salaried') NOT NULL
);


CREATE TABLE ClubMember (
    clubMemberID INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    dateOfBirth DATE NOT NULL,
    height DECIMAL(5,2),  
    weight DECIMAL(5,2),  
    SSN CHAR(11) UNIQUE,
    medicareNumber CHAR(12) UNIQUE,
    phoneNumber VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(50),
    postalCode VARCHAR(10),
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
);


CREATE TABLE FamilyMember (
    SSN CHAR(11) PRIMARY KEY,
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    dateOfBirth DATE NOT NULL,
    medicareNumber CHAR(12) UNIQUE NOT NULL,
    phoneNumber VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(50),
    postalCode VARCHAR(10),
    email VARCHAR(100)
);

CREATE TABLE Hobby (
    name VARCHAR(50) PRIMARY KEY
);

CREATE TABLE Payment (
    paymentID INT AUTO_INCREMENT PRIMARY KEY,
    paymentDate DATE,
    amount DECIMAL(8,2),
    method ENUM('Cash', 'Debit', 'Credit'),
    forYear INT
);

-- Relations

-- Make sure this is many to one (personnel can only work at 1 location) 
CREATE TABLE WorksAt (
    personnelID INT,
    locationID INT,
    startDate DATE,
    endDate DATE,
    PRIMARY KEY (personnelID, locationID, startDate),
    FOREIGN KEY (personnelID) REFERENCES Personnel(personnelID),
    FOREIGN KEY (locationID) REFERENCES Location(locationID)
);

-- many to many relation (many members could have many hobbies) 
CREATE TABLE HasHobby (
    clubMemberID INT,
    hobbyName VARCHAR(50),
    PRIMARY KEY (clubMemberID, hobbyName),
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY (hobbyName) REFERENCES Hobby(name)
);

CREATE TABLE RelatedTo (
    clubMemberID INT,
    familyMemberSSN CHAR(11),
    relationship ENUM('Father', 'Mother','GrandMother','GrandFather', 'Tutor','Partner','Friend', 'Other'),
    PRIMARY KEY (clubMemberID, familyMemberSSN),
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY (familyMemberSSN) REFERENCES FamilyMember(SSN)
);

CREATE TABLE LocationCalledAt (
	number VARCHAR(20) PRIMARY KEY,
    locationID INT,
    FOREIGN KEY (locationID) REFERENCES Location(locationID)
);

CREATE TABLE MakesPayment (
	clubMemberID INT, 
    paymentID INT PRIMARY KEY,
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY (paymentID) REFERENCES Payment(paymentID)
);


-- Create improved EnrolledAt table
CREATE TABLE EnrolledAt (
    clubMemberID INT,
    locationID INT,
    startDate DATE NOT NULL,
    endDate DATE DEFAULT NULL,
    PRIMARY KEY (clubMemberID, locationID, startDate),
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID),
    FOREIGN KEY (locationID) REFERENCES Location(locationID)
);





INSERT INTO Location (name, type, address, city, province, postalCode, webAddress, maxCapacity) VALUES
('MVC HQ', 'Head', '123 Volleyball St', 'Montreal', 'QC', 'H2X 1Y4', 'https://mvc.ca/hq', 500), 
('MVC Laval', 'Branch', '456 Court Ave', 'Laval', 'QC', 'H7A 2Z9', 'https://mvc.ca/laval', 300),
('MVC Westmount', 'Branch', '789 Sports Blvd', 'Westmount', 'QC', 'H3Y 3K4', 'https://mvc.ca/westmount', 250),
('MVC Brossard', 'Branch', '321 Beach Rd', 'Brossard', 'QC', 'J4Y 1B1', 'https://mvc.ca/brossard', 200),
('MVC Longueuil', 'Branch', '100 Main St', 'Longueuil', 'QC', 'J4H 3G5', 'https://mvc.ca/longueuil', 150),
('MVC NDG', 'Branch', '111 Champs Ave', 'Montreal', 'QC', 'H4A 3M1', 'https://mvc.ca/ndg', 275),
('MVC Verdun', 'Branch', '222 Point St', 'Verdun', 'QC', 'H4G 2J1', 'https://mvc.ca/verdun', 300),
('MVC Rosemont', 'Branch', '333 Sports Pl', 'Montreal', 'QC', 'H1Y 1L3', 'https://mvc.ca/rosemont', 220),
('MVC Anjou', 'Branch', '444 Volley Rd', 'Anjou', 'QC', 'H1K 4A5', 'https://mvc.ca/anjou', 200),
('MVC Plateau', 'Branch', '555 Club St', 'Montreal', 'QC', 'H2W 1S1', 'https://mvc.ca/plateau', 180);

SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM 
    information_schema.KEY_COLUMN_USAGE 
WHERE 
    REFERENCED_TABLE_NAME = 'EnrolledAt';


INSERT INTO RoleIn (clubMemberID, formationID, role) VALUES
(32, 3, 'outside hitter'),
(30, 3, 'setter'),
(35, 3, 'middle blocker'),
(27, 3, 'libero'),
(33, 3, 'opposite hitter'),
(28, 4, 'outside hitter'),
(26, 4, 'setter'),
(29, 4, 'middle blocker'),
(34, 4, 'libero'),
(31, 4, 'opposite hitter');





 ##minor members entries
INSERT INTO ClubMember (firstName, lastName, dateOfBirth, height, weight, SSN, medicareNumber, phoneNumber, address, city, province, postalCode, status) VALUES
('Ava', 'Brown', '2009-02-15', 154.6, 56.8, '123-45-6810', 'MCR12345M001', '514-100-1001', '100 Minor St', 'Montreal', 'QC', 'H2X 1A1', 'Active'),
('Liam', 'Smith', '2013-03-05', 162.1, 56.7, '123-45-6811', 'MCR12345M002', '514-100-1002', '101 Minor St', 'Montreal', 'QC', 'H2X 2A2', 'Inactive'),
('Mia', 'Garcia', '2009-03-22', 169.0, 37.3, '123-45-6812', 'MCR12345M003', '514-100-1003', '102 Minor St', 'Montreal', 'QC', 'H2X 3A3', 'Active'),
('Noah', 'Johnson', '2009-09-08', 146.7, 39.5, '123-45-6813', 'MCR12345M004', '514-100-1004', '103 Minor St', 'Montreal', 'QC', 'H2X 4A4', 'Inactive'),
('Emma', 'Martinez', '2012-06-15', 155.0, 53.7, '123-45-6814', 'MCR12345M005', '514-100-1005', '104 Minor St', 'Montreal', 'QC', 'H2X 5A5', 'Active'),
('Lucas', 'Lee', '2012-10-12', 151.4, 48.0, '123-45-6815', 'MCR12345M006', '514-100-1006', '105 Minor St', 'Montreal', 'QC', 'H2X 6A6', 'Active'),
('Sophia', 'Rodriguez', '2011-11-01', 148.1, 53.8, '123-45-6816', 'MCR12345M007', '514-100-1007', '106 Minor St', 'Montreal', 'QC', 'H2X 7A7', 'Inactive'),
('Ethan', 'Lopez', '2009-05-31', 166.7, 56.8, '123-45-6817', 'MCR12345M008', '514-100-1008', '107 Minor St', 'Montreal', 'QC', 'H2X 8A8', 'Active'),
('Isabella', 'Wilson', '2009-03-07', 148.6, 60.9, '123-45-6818', 'MCR12345M009', '514-100-1009', '108 Minor St', 'Montreal', 'QC', 'H2X 9A9', 'Inactive'),
('Logan', 'Anderson', '2008-12-27', 158.5, 64.6, '123-45-6819', 'MCR12345M010', '514-100-1010', '109 Minor St', 'Montreal', 'QC', 'H2X 10A10', 'Active');

 ##major members entries
INSERT INTO ClubMember (firstName, lastName, dateOfBirth, height, weight, SSN, medicareNumber, phoneNumber, address, city, province, postalCode, status) VALUES
('Olivia', 'Walker', '1991-06-03', 165.5, 64.3, '123-45-6710', 'MCR12345A001', '514-200-2001', '200 Major St', 'Montreal', 'QC', 'H3X 1B1', 'Active'),
('James', 'Hall', '1994-10-29', 182.2, 61.2, '123-45-6711', 'MCR12345A002', '514-200-2002', '201 Major St', 'Montreal', 'QC', 'H3X 2B2', 'Inactive'),
('Charlotte', 'Allen', '1994-09-23', 168.9, 55.9, '123-45-6712', 'MCR12345A003', '514-200-2003', '202 Major St', 'Montreal', 'QC', 'H3X 3B3', 'Active'),
('Benjamin', 'Young', '1991-07-04', 174.2, 61.1, '123-45-6713', 'MCR12345A004', '514-200-2004', '203 Major St', 'Montreal', 'QC', 'H3X 4B4', 'Active'),
('Amelia', 'King', '2000-08-26', 173.6, 69.9, '123-45-6714', 'MCR12345A005', '514-200-2005', '204 Major St', 'Montreal', 'QC', 'H3X 5B5', 'Inactive'),
('Henry', 'Wright', '2002-05-11', 187.3, 61.7, '123-45-6715', 'MCR12345A006', '514-200-2006', '205 Major St', 'Montreal', 'QC', 'H3X 6B6', 'Active'),
('Harper', 'Scott', '2005-07-16', 180.9, 82.9, '123-45-6716', 'MCR12345A007', '514-200-2007', '206 Major St', 'Montreal', 'QC', 'H3X 7B7', 'Inactive'),
('Roger', 'Smith', '1985-01-01', 180.5, 75.0, '111-22-3333', 'MCR99999R001', '514-111-2222','101 Coach Lane', 'Montreal', 'QC', 'H2X 1A2', 'Active'),
('Amanda', 'Lee', '1990-02-02', 165.0, 60.0, '111-22-3334', 'MCR99999A002', '514-222-3333','102 Admin Rd', 'Laval', 'QC', 'H7A 2B3', 'Active'),
('Victor', 'Nguyen', '1982-03-03', 172.0, 68.0, '111-22-3335', 'MCR99999V003', '514-333-4444','103 Net St', 'Montreal', 'QC', 'H2X 3C4', 'Active'),

('Brandon', 'Cole', '1992-01-12', 180.5, 75.2, '123-45-6725', 'MCR12345A015', '514-300-3006', '215 Major St', 'Montreal', 'QC', 'H3X 13B13', 'Active'), #60
('Tyler', 'Morgan', '1991-06-18', 182.3, 76.9, '123-45-6726', 'MCR12345A016', '514-300-3007', '216 Major St', 'Montreal', 'QC', 'H3X 14B14', 'Active'),
('Dylan', 'Bailey', '1993-03-25', 179.0, 73.4, '123-45-6727', 'MCR12345A017', '514-300-3008', '217 Major St', 'Montreal', 'QC', 'H3X 15B15', 'Active'),
('Justin', 'Ramirez', '1987-09-10', 184.7, 80.0, '123-45-6728', 'MCR12345A018', '514-300-3009', '218 Major St', 'Montreal', 'QC', 'H3X 16B16', 'Active'),
('Eric', 'Gonzalez', '1986-05-14', 177.8, 74.1, '123-45-6729', 'MCR12345A019', '514-300-3010', '219 Major St', 'Montreal', 'QC', 'H3X 17B17', 'Active'),
('Zachary', 'Ross', '1990-08-30', 186.2, 83.7, '123-45-6730', 'MCR12345A020', '514-300-3011', '220 Major St', 'Montreal', 'QC', 'H3X 18B18', 'Active'),
('Blake', 'Diaz', '1992-04-19', 181.6, 78.3, '123-45-6731', 'MCR12345A021', '514-300-3012', '221 Major St', 'Montreal', 'QC', 'H3X 19B19', 'Active'),
('Shawn', 'Murray', '1988-02-07', 178.9, 72.6, '123-45-6732', 'MCR12345A022', '514-300-3013', '222 Major St', 'Montreal', 'QC', 'H3X 20B20', 'Active'),
('Adrian', 'Peterson', '1994-11-11', 183.0, 79.5, '123-45-6733', 'MCR12345A023', '514-300-3014', '223 Major St', 'Montreal', 'QC', 'H3X 21B21', 'Active'),
('Jared', 'Reynolds', '1993-10-02', 185.1, 77.9, '123-45-6734', 'MCR12345A024', '514-300-3015', '224 Major St', 'Montreal', 'QC', 'H3X 22B22', 'Active'); #69





 ##family members to the minor members
INSERT INTO FamilyMember (SSN, firstName, lastName, dateOfBirth, medicareNumber, phoneNumber, address, city, province, postalCode, email) VALUES
('222-33-6600', 'John', 'Brown', '1985-03-16', 'FMCR54321000', '438-555-2200', '300 Family St', 'Montreal', 'QC', 'H4X 0F0', 'john.brown@email.com'),
('222-33-6601', 'Emily', 'Smith', '1972-05-01', 'FMCR54321001', '438-555-2201', '301 Family St', 'Montreal', 'QC', 'H4X 1F1', 'emily.smith@email.com'),
('222-33-6602', 'David', 'Garcia', '1985-12-18', 'FMCR54321002', '438-555-2202', '302 Family St', 'Montreal', 'QC', 'H4X 2F2', 'david.garcia@email.com'),
('222-33-6603', 'Sarah', 'Johnson', '1988-07-03', 'FMCR54321003', '438-555-2203', '303 Family St', 'Montreal', 'QC', 'H4X 3F3', 'sarah.johnson@email.com'),
('222-33-6604', 'Michael', 'Martinez', '1989-05-13', 'FMCR54321004', '438-555-2204', '304 Family St', 'Montreal', 'QC', 'H4X 4F4', 'michael.martinez@email.com'),
('222-33-6605', 'Laura', 'Lee', '1970-10-16', 'FMCR54321005', '438-555-2205', '305 Family St', 'Montreal', 'QC', 'H4X 5F5', 'laura.lee@email.com'),
('222-33-6606', 'Daniel', 'Rodriguez', '1982-08-03', 'FMCR54321006', '438-555-2206', '306 Family St', 'Montreal', 'QC', 'H4X 6F6', 'daniel.rodriguez@email.com'),
('222-33-6607', 'Grace', 'Lopez', '1978-09-13', 'FMCR54321007', '438-555-2207', '307 Family St', 'Montreal', 'QC', 'H4X 7F7', 'grace.lopez@email.com'),
('222-33-6608', 'Andrew', 'Wilson', '1982-06-03', 'FMCR54321008', '438-555-2208', '308 Family St', 'Montreal', 'QC', 'H4X 8F8', 'andrew.wilson@email.com'),
('222-33-6609', 'Karen', 'Anderson', '1983-07-23', 'FMCR54321009', '438-555-2209', '309 Family St', 'Montreal', 'QC', 'H4X 9F9', 'karen.anderson@email.com');







 ##relation to the minor members, we can change up relations if we want to like tutor and whatnot
INSERT INTO RelatedTo (clubMemberID, familyMemberSSN, relationship) VALUES
(1, '222-33-6600', 'Father'),
(2, '222-33-6601', 'Mother'),
(3, '222-33-6602', 'Father'),
(4, '222-33-6603', 'Mother'),
(5, '222-33-6604', 'Father'),
(6, '222-33-6605', 'Mother'),
(7, '222-33-6606', 'Father'),
(8, '222-33-6607', 'Mother'),
(9, '222-33-6608', 'Father'),
(10, '222-33-6609', 'Mother');







INSERT INTO Personnel (SSN, firstName, lastName, dateOfBirth, medicareNumber, phoneNumber, address, city, province, postalCode, email, role, mandate) VALUES
('111-22-3333', 'Roger', 'Smith', '1985-01-01', 'PERS123456A', '514-111-2222', '101 Coach Lane', 'Montreal', 'QC', 'H2X 1A2', 'roger.smith@mvc.ca', 'Coach', 'Salaried'),
('111-22-3334', 'Amanda', 'Lee', '1990-02-02', 'PERS123456B', '514-222-3333', '102 Admin Rd', 'Laval', 'QC', 'H7A 2B3', 'amanda.lee@mvc.ca', 'Administrator', 'Salaried'),
('111-22-3335', 'Victor', 'Nguyen', '1982-03-03', 'PERS123456C', '514-333-4444', '103 Net St', 'Montreal', 'QC', 'H2X 3C4', 'victor.nguyen@mvc.ca', 'Captain', 'Volunteer'),
('111-22-3336', 'Diana', 'Chen', '1991-04-04', 'PERS123456D', '514-444-5555', '104 Line Ave', 'Westmount', 'QC', 'H3Z 4D5', 'diana.chen@mvc.ca', 'Coach', 'Salaried'),
('111-22-3337', 'George', 'Lopez', '1978-05-05', 'PERS123456E', '514-555-6666', '105 Serve Blvd', 'Montreal', 'QC', 'H2X 5E6', 'george.lopez@mvc.ca', 'Other', 'Volunteer'),
('111-22-3338', 'Emma', 'Watson', '1993-06-06', 'PERS123456F', '514-666-7777', '106 Block Rd', 'Laval', 'QC', 'H7A 6F7', 'emma.watson@mvc.ca', 'Coach', 'Salaried'),
('111-22-3339', 'Daniel', 'Singh', '1980-07-07', 'PERS123456G', '514-777-8888', '107 Smash St', 'Montreal', 'QC', 'H2X 7G8', 'daniel.singh@mvc.ca', 'Administrator', 'Volunteer'),
('111-22-3340', 'Sophia', 'Brown', '1989-08-08', 'PERS123456H', '514-888-9999', '108 Set Way', 'Montreal', 'QC', 'H2X 8H9', 'sophia.brown@mvc.ca', 'General Manager', 'Salaried'),
('111-22-3341', 'Brian', 'Doe', '1975-09-09', 'PERS123456I', '514-999-0000', '109 Jump Rd', 'Anjou', 'QC', 'H1K 9I0', 'brian.doe@mvc.ca', 'Other', 'Volunteer'),
('111-22-3342', 'Isabelle', 'Smith', '1987-10-10', 'PERS123456J', '514-000-1111', '110 Tip St', 'Montreal', 'QC', 'H2X 0J1', 'isabelle.smith@mvc.ca', 'Assistant Coach', 'Salaried');





INSERT INTO Hobby (name) VALUES
('Volleyball'),
('Soccer'),
('Tennis'),
('Ping Pong'),
('Swimming'),
('Hockey'),
('Golf'),
('Basketball'),
('Running'),
('Cycling');







INSERT INTO HasHobby (clubMemberID, hobbyName) VALUES
(1, 'Soccer'),(1, 'Swimming'),(1, 'Tennis'),(2, 'Tennis'),(3, 'Running'),(3, 'Cycling'),
(3, 'Golf'),(4, 'Basketball'),(5, 'Basketball'),(5, 'Swimming'),(6, 'Basketball'),
(6, 'Swimming'),(7, 'Soccer'),(8, 'Golf'),(8, 'Basketball'),(8, 'Swimming'),
(9, 'Cycling'),(9, 'Hockey'),(10, 'Golf'),(10, 'Basketball'),(11, 'Tennis'),
(11, 'Swimming'),(11, 'Running'),(12, 'Ping Pong'),(12, 'Swimming'),(12, 'Golf'),
(13, 'Soccer'),(14, 'Soccer'),(15, 'Volleyball'),(15, 'Swimming'),(15, 'Basketball'),
(16, 'Swimming'),(16, 'Soccer'),(16, 'Cycling'),(17, 'Basketball'),
(17, 'Tennis'),(17, 'Volleyball'),(18, 'Swimming'),(19, 'Ping Pong'),
(19, 'Golf'),(20, 'Hockey'),(20, 'Soccer'),(20, 'Ping Pong');







INSERT INTO Payment (paymentID, paymentDate, amount, method, forYear) VALUES
(1, '2024-07-05', 106.73, 'Cash', 2024),
(2, '2024-12-19', 99.56, 'Cash', 2024),
(3, '2025-05-06', 102.59, 'Cash', 2025),
(4, '2024-02-09', 108.7, 'Cash', 2024),
(5, '2023-09-19', 48.64, 'Cash', 2023),
(6, '2023-08-25', 96.2, 'Debit', 2023),
(7, '2024-06-28', 74.53, 'Debit', 2024),
(8, '2024-12-22', 65.39, 'Debit', 2024),
(9, '2024-10-27', 106.56, 'Credit', 2024),
(10, '2025-05-18', 90.74, 'Credit', 2025),
(11, '2024-05-10', 104.15, 'Debit', 2024),
(12, '2025-01-13', 60.69, 'Credit', 2025),
(13, '2025-02-13', 114.51, 'Cash', 2025),
(14, '2023-08-23', 50.48, 'Cash', 2023),
(15, '2025-01-11', 108.76, 'Debit', 2025),
(16, '2025-02-07', 49.15, 'Cash', 2025),
(17, '2024-08-27', 43.08, 'Debit', 2024),
(18, '2024-10-23', 48.98, 'Credit', 2024),
(19, '2024-09-24', 44.54, 'Debit', 2024),
(20, '2025-04-23', 64.72, 'Debit', 2025),
(21, '2024-12-22', 73.61, 'Debit', 2024),
(22, '2025-04-14', 57.42, 'Debit', 2025),
(23, '2024-07-28', 98.72, 'Debit', 2024),
(24, '2024-11-16', 103.07, 'Credit', 2024),
(25, '2024-08-23', 124.14, 'Debit', 2024),
(26, '2023-08-10', 126.73, 'Debit', 2023),
(27, '2024-09-06', 97.68, 'Cash', 2024),
(28, '2024-05-26', 81.8, 'Credit', 2024),
(29, '2025-03-01', 151.21, 'Credit', 2025),
(30, '2025-01-20', 109.99, 'Cash', 2025),
(31, '2025-01-31', 75.86, 'Credit', 2025),
(32, '2024-06-03', 149.69, 'Cash', 2024),
(33, '2024-05-12', 204.35, 'Cash', 2024),
(34, '2024-08-06', 192.61, 'Cash', 2024),
(35, '2024-09-22', 161.01, 'Credit', 2024),
(36, '2025-04-02', 117.18, 'Debit', 2025),
(37, '2023-12-18', 126.26, 'Debit', 2023),
(38, '2023-12-26', 177.56, 'Cash', 2023),
(39, '2025-03-31', 86.31, 'Cash', 2025),
(40, '2024-10-13', 225.27, 'Debit', 2025),
(41, '2025-06-19', 70.7, 'Debit', 2025),
(42, '2024-11-20', 273.33, 'Cash', 2025),
(43, '2023-11-29', 90.38, 'Credit', 2023),
(44, '2024-10-29', 181.31, 'Debit', 2024),
(45, '2023-12-17', 263.39, 'Debit', 2025);







INSERT INTO MakesPayment (clubMemberID, paymentID) VALUES
(1, 1),(1, 2),(1, 3),(2, 4),(2, 5),
(2, 6),(3, 7),(3, 8),(3, 9),(4, 10),
(4, 11),(5, 12),(5, 13),(6, 14),(6, 15),
(6, 16),(7, 17),(7, 18),(7, 19),(8, 20),
(8, 21),(9, 22),(10, 23),(10, 24),(11, 25),
(11, 26),(11, 27),(12, 28),(12, 29),(13, 30),
(13, 31),(13, 32),(14, 33),(15, 34),(16, 35),
(16, 36),(17, 37),(18, 38),(18, 39),(18, 40),
(19, 41),(19, 42),(20, 43),(20, 44),(20, 45);







INSERT INTO WorksAt (personnelID, locationID, startDate, endDate) VALUES
(1, 4, '2025-05-03', '2025-06-09'),
(2, 4, '2025-02-25', NULL),
(3, 5, '2025-06-08', '2025-06-28'),
(4, 3, '2023-08-18', '2024-03-06'),
(5, 3, '2024-08-18', '2025-02-22'),
(6, 2, '2024-05-27', '2025-01-17'),
(7, 3, '2024-07-22', NULL),
(8, 1, '2024-04-21', '2024-10-07'),
(9, 3, '2024-08-13', '2024-10-06'),
(10, 5, '2024-02-21', '2024-06-08');




INSERT INTO EnrolledAt (clubMemberID, locationID, startDate) VALUES
(1, 3, '2020-06-15'),
(2, 5, '2022-09-01'),
(3, 2, '2023-01-20'),
(4, 2, '2021-03-12'),
(5, 5, '2019-11-07'),
(6, 4, '2023-07-30'),
(7, 5, '2020-02-18'),
(8, 3, '2021-05-10'),
(9, 4, '2022-08-23'),
(10, 4, '2019-10-02'),
(11, 3, '2023-12-01'),
(12, 1, '2020-04-25'),
(13, 3, '2024-01-17'),
(14, 5, '2022-11-11'),
(15, 1, '2020-07-03'),
(16, 4, '2023-02-22'),
(17, 5, '2021-06-09'),
(18, 3, '2019-09-14'),
(19, 2, '2023-03-05'),
(20, 2, '2021-08-29'),
(26, 3, '2020-01-19'),
(27, 3, '2022-05-06'),
(28, 3, '2023-06-12'),
(29, 3, '2019-12-31'),
(30, 3, '2024-03-09'),
(33, 4, '2023-11-14'),
(35, 4, '2021-10-16');








INSERT INTO LocationCalledAt (number, locationID) VALUES
('660-487-6475', 3),
('938-242-1948', 1),
('924-115-7815', 2),
('659-387-7840', 3),
('801-609-7535', 2),
('139-332-8711', 3),
('587-148-4185', 4),
('839-894-7196', 1),
('593-423-2094', 1),
('711-220-1868', 2),
('483-396-9477', 2),
('515-917-9533', 1),
('041-352-5601', 5),
('230-989-1013', 5),
('991-615-1090', 5);

INSERT INTO Team (teamID, name, gender) VALUES
(1, 'Falcons', 'Male'),
(2, 'Tigers', 'Female'),
(3, 'Hawks', 'Male'),
(4, 'Eagles', 'Male'),
(5, 'Wolves', 'Male'),
(6, 'Panthers', 'Female'),
(7, 'Bears', 'Male'),
(8, 'Lions', 'Female'),
(9, 'Sharks', 'Male'),
(10, 'Cheetahs', 'Female'),
(21, 'Dogs', 'Male'),
(22, 'Cats', 'Female'),
(23, 'girls1', 'Female'),
(24, 'girls2', 'Female'),
(25, 'boys1', 'Male'),
(26, 'boys2', 'Male');

#Q10 requirements

INSERT INTO RoleIn (clubMemberID, teamID, role) VALUES
(60, 3, 'outside hitter'),
(61, 3, 'setter'),
(62, 3, 'middle blocker'),
(63, 3, 'libero'),
(64, 3, 'opposite hitter'),
(65, 4, 'outside hitter'),
(66, 4, 'setter'),
(67, 4, 'middle blocker'),
(68, 4, 'libero'),
(69, 4, 'opposite hitter');

SELECT * FROM ClubMember;

INSERT INTO TeamMatchUp (formationID, team1ID, team2ID, locationID, date, startTime, sessionType, team1Score, team2Score) VALUES
(1, 3, 4, 3, '2025-08-04', '17:00:00', 'Game', 18, 24);



INSERT INTO Personnel (
    SSN, firstName, lastName, dateOfBirth, medicareNumber, phoneNumber, address, city, province, postalCode, email, role, mandate) VALUES 
('211-23-3342', 'joseph', 'richmand', '1980-03-15', 'PERS165646J', '514-423-3324','200 Coach Ln', 'Montreal', 'QC', 'H1X 1X1', 'joseph.richmand@example.com', 'Head Coach', 'Salaried'),
('121-42-3342', 'James', 'Thomyguyun', '1975-11-25', 'PERS165346J', '514-234-6574','210 Coach St', 'Montreal', 'QC', 'H1Y 2Y2', 'James.Thomyguyun@example.com', 'Head Coach', 'Salaried');


CREATE TABLE HeadCoachOf (
  teamID INT UNIQUE PRIMARY KEY,
  personnelID INT,
  FOREIGN KEY (personnelID) REFERENCES Personnel(personnelID),
  FOREIGN KEY (teamID) REFERENCES Team(teamID)
);

 INSERT INTO HeadCoachOf (teamID,personnelID) VALUES
 (3 , 23),
 (4 , 24);
 
 

#------------

INSERT INTO ClubMember (
    clubMemberID, firstName, lastName, dateOfBirth, height, weight, SSN, medicareNumber,
    phoneNumber, address, city, province, postalCode, status
) VALUES 
    (996, 'Tina', 'sentiak', '2004-04-11', 165.2, 60.5, '999-68-7667', 'ZZZ799448777', '4501734567', '123 Maine Street', 'Montreal', 'QC', 'H3Z2X3', 'Inactive'),
    (997, 'Jerimiah', 'Acesus', '2002-04-11', 185.2, 64.5, '991-68-7667', 'ZZZ795548777', '4505634567', '23 Monte Street', 'Montreal', 'QC', 'H7X3G6', 'Inactive');




-- 2. Enroll the member in 2 different locations (e.g., locationID 1 and 2)
INSERT INTO EnrolledAt (clubMemberID, locationID, startDate, endDate) VALUES 
  (996, 1, '2020-01-01', '2022-12-31'),
  (996, 2, '2023-01-01', NULL),
  (997, 3, '2021-03-01', '2021-12-31'),
  (997, 4, '2022-02-01', NULL);



-- 3. Insert a payment made 2+ years ago (e.g., paymentID 5001)
INSERT INTO Payment (
    paymentID, paymentDate, amount, method, forYear
) VALUES (
    11111, '2022-07-15', 20.00, 'Credit', 2021
);

-- 4. Link the payment to the member
INSERT INTO MakesPayment (clubMemberID, paymentID) VALUES
    (996, 11111);



##########################################################################################
select * FROM RoleIn;
select * FROM TeamMatchUp;
DELETE FROM TeamMatchUp
WHERE FormationID = 1; 









###########################################################################################
                                ##TRIGGER SECTION##        
###########################################################################################   


-- TODO: test this CHECK PERSONNEL HEAD COACH ELIGIBILITY
DELIMITER $$
-- RelatedToRoleIn
CREATE TRIGGER check_head_coach_role
BEFORE INSERT ON HeadCoachOf
FOR EACH ROW
BEGIN
  DECLARE p_role VARCHAR(50);

  -- Get the role of the personnel being inserted
  SELECT role INTO p_role
  FROM Personnel
  WHERE personnelID = NEW.personnelID;

  -- Check role validity
  IF p_role NOT IN ('Coach', 'Head Coach') THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Personnel must be a Coach or Head Coach to be assigned as head coach.';
  END IF;
END $$

DELIMITER ;


#Enforce Gender and Location Consistency Within Team


DELIMITER $$

CREATE PROCEDURE AssignClubMemberToFormation(
    IN p_formationID INT,
    IN p_clubMemberID INT,
    IN p_teamID INT,
    IN p_role VARCHAR(50)
)
BEGIN
    DECLARE team1 INT;
    DECLARE team2 INT;
    DECLARE cmGender ENUM('Male', 'Female');
    DECLARE teamGender ENUM('Male', 'Female');
    DECLARE cmLoc INT;
    DECLARE teamLoc INT;

    -- Validate: team must be part of formation
    SELECT team1ID, team2ID INTO team1, team2
    FROM TeamMatchUp
    WHERE formationID = p_formationID;

    IF p_teamID NOT IN (team1, team2) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Team is not part of formation';
    END IF;

    -- Validate: gender match
    SELECT gender INTO cmGender FROM ClubMember WHERE clubMemberID = p_clubMemberID;
    SELECT gender INTO teamGender FROM Team WHERE teamID = p_teamID;

    IF cmGender <> teamGender THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Gender mismatch between the member and team';
    END IF;

    -- Validate: location match
    SELECT locationID INTO cmLoc FROM EnrolledAt WHERE clubMemberID = p_clubMemberID AND endDate IS NULL;
    SELECT locationID INTO teamLoc FROM TeamLocated WHERE teamID = p_teamID;

    IF cmLoc <> teamLoc THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Location mismatch between the member and team';
    END IF;

    -- Insert into InTeam if not already there
    INSERT IGNORE INTO InTeam (clubMemberID, teamID) VALUES (p_clubMemberID, p_teamID);

    -- Insert into RoleIn
    INSERT INTO RoleIn (formationID, clubMemberID, role) VALUES (p_formationID, p_clubMemberID, p_role);

END$$

DELIMITER ;



DELIMITER $$

CREATE TRIGGER trg_check_team_gender
BEFORE INSERT ON TeamMatchUp
FOR EACH ROW
BEGIN
    DECLARE gender1 ENUM('Male', 'Female');
    DECLARE gender2 ENUM('Male', 'Female');

    -- Get gender of both teams
    SELECT gender INTO gender1 FROM Team WHERE teamID = NEW.team1ID;
    SELECT gender INTO gender2 FROM Team WHERE teamID = NEW.team2ID;

    -- Compare genders
    IF gender1 <> gender2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Both teams in a formation must be of the same gender.';
    END IF;
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER trg_check_team_gender_update
BEFORE UPDATE ON TeamMatchUp
FOR EACH ROW
BEGIN
    DECLARE gender1 ENUM('Male', 'Female');
    DECLARE gender2 ENUM('Male', 'Female');

    -- Get gender of both teams
    SELECT gender INTO gender1 FROM Team WHERE teamID = NEW.team1ID;
    SELECT gender INTO gender2 FROM Team WHERE teamID = NEW.team2ID;

    -- Compare genders
    IF gender1 <> gender2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Both teams in a formation must be of the same gender.';
    END IF;
END$$

DELIMITER ;





-- dont need ?
-- DELIMITER $$

-- CREATE TRIGGER trg_insert_inteam_after_formation
-- AFTER INSERT ON RoleIn
-- FOR EACH ROW
-- BEGIN
--     -- Check if player is already in the team
--     IF NOT EXISTS (
--         SELECT 1 FROM InTeam
--         WHERE clubMemberID = NEW.clubMemberID AND teamID = NEW.teamID
--     ) THEN
--         INSERT INTO InTeam (clubMemberID, teamID)
--         VALUES (NEW.clubMemberID, NEW.teamID);
--     END IF;
-- END$$

-- DELIMITER ;


 -- doesnt work now might need 
-- DELIMITER $$

-- CREATE TRIGGER trg_delete_inteam_after_formation
-- AFTER DELETE ON FormationPlayers
-- FOR EACH ROW
-- BEGIN
--     -- Check if the member is still part of any other formation in the same team
--     IF NOT EXISTS (
--         SELECT 1 FROM FormationPlayers
--         WHERE clubMemberID = OLD.clubMemberID AND teamID = OLD.teamID
--     ) THEN
--         DELETE FROM InTeam
--         WHERE clubMemberID = OLD.clubMemberID AND teamID = OLD.teamID;
--     END IF;
-- END$$

-- DELIMITER ;



#Prevent Overlapping Formations (Minimum 3 Hours Apart)

DELIMITER $$
CREATE TRIGGER trg_check_schedule_conflict
BEFORE INSERT ON RoleIn
FOR EACH ROW
BEGIN
    DECLARE newDate DATE;
    DECLARE newStart TIME;

    SELECT date, startTime INTO newDate, newStart
    FROM TeamMatchUp
    WHERE formationID = NEW.formationID;

    IF EXISTS (
        SELECT 1
        FROM RoleIn fp
        JOIN TeamFormation tf ON tf.formationID = fp.formationID
        WHERE fp.clubMemberID = NEW.clubMemberID
        AND tf.date = newDate
        AND ABS(TIMESTAMPDIFF(MINUTE, tf.startTime, newStart)) < 180
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Player is already in another formation within 3 hours.';
    END IF;
END$$

DELIMITER ;



#Triggers active club members if sufficient payment is made
 DELIMITER $$

CREATE TRIGGER check_membership_payment
AFTER INSERT ON MakesPayment
FOR EACH ROW
BEGIN
    DECLARE memberDOB DATE;
    DECLARE paymentYear INT;
    DECLARE age INT;
    DECLARE totalPaid DECIMAL(10,2);

    -- Get the member's DOB
    SELECT dateOfBirth INTO memberDOB
    FROM ClubMember
    WHERE clubMemberID = NEW.clubMemberID;

    -- Get the payment year
    SELECT forYear INTO paymentYear
    FROM Payment
    WHERE paymentID = NEW.paymentID;

    -- Calculate age as of Jan 1st of that year
    SET age = TIMESTAMPDIFF(YEAR, memberDOB, STR_TO_DATE(CONCAT(paymentYear, '-01-01'), '%Y-%m-%d'));

    -- Sum all payments made by this member for that year
    SELECT SUM(P.amount) INTO totalPaid
    FROM MakesPayment MP
    JOIN Payment P ON MP.paymentID = P.paymentID
    WHERE MP.clubMemberID = NEW.clubMemberID
      AND P.forYear = paymentYear;

    -- Activate the member if payment threshold is met
    IF (age < 18 AND totalPaid >= 100) OR (age >= 18 AND totalPaid >= 200) THEN
        UPDATE ClubMember
        SET status = 'Active'
        WHERE clubMemberID = NEW.clubMemberID;
    END IF;

END$$

DELIMITER ;









