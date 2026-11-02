-- Related academic schema draft imported from AleCondina/MVC_database.
-- Source blob: 26c680308c383b1a6a068b7735a72d6ade77aa69
-- Syntax cleanup only: removed the Payment trailing comma and fixed comment spacing.
-- This is NOT a complete schema for the PHP application. See README.md.

CREATE TABLE Location (
    locationID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('Head', 'Branch') NOT NULL,
    address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(50),
    postalCode VARCHAR(10),
    phoneNumber VARCHAR(20),
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
    role ENUM('Administrator', 'Captain', 'Coach', 'Assistant Coach', 'Other') NOT NULL,
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
    locationID INT,
    FOREIGN KEY (locationID) REFERENCES Location(locationID)
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

-- make sure this is many to one (personnel can only work at 1 location) 
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





