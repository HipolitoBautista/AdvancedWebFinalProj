
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `awt` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `awt`;
DROP TABLE IF EXISTS `applicant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicant` (
  `id` int NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicant` WRITE;
/*!40000 ALTER TABLE `applicant` DISABLE KEYS */;
INSERT INTO `applicant` VALUES (1,'iro','od','johndoeee123','$2y$10$HpJUA5TifLk4Ql1kMoIevelCQJlvYUy6kdaACUUO7nyMTgh/FK182','USA'),(2,'Fitzgerald','Grimbaldeston','fgrimbaldeston1','$2a$04$izP9dHFLxqrFD3FysGsIeOnx9uMDzmP0oiRr9yBTj18dlAUcRm18e','China'),(3,'Betsey','Davie','bdavie2','$2a$04$qXz5TbzXkiJY2kqe2MMMbO4Q6md87N5FMN7ZJ7oPqu3hIHAnofNEW','China'),(4,'Lorine','Rubinchik','lrubinchik3','$2a$04$QlgACO5kDEZZk.E4YuZSR.cLaWPEKLAcGInLgmt3vZLfJmdnDFtD2','Russia'),(5,'Ronnica','Free','rfree4','$2a$04$yzKU7wF.T7bFBdnCpaIVDuFBtwBXTs4zlT8hVMIit6EWK9WJ4pDx2','Philippines'),(6,'Jaquelyn','Crighton','jcrighton5','$2a$04$WETBaFDXTlMO6GgJIBMKNOTNDLoFyMbGjd0GPKCh.tPPAgX64IYAa','China'),(7,'Antonio','Vezey','avezey6','$2a$04$1N69yd68UOJgVmzvgXAMHuJ2IdJUkjk.CjxMZjwRrP6lkFYFww3bK','Philippines'),(8,'Johan','McElhargy','jmcelhargy7','$2a$04$ehLEBAllWbwFX1Edw4VD/OR0Yyqoa5paTzwebDsCU3oI3zeHMOjjK','Peru'),(9,'Mil','Bramford','mbramford8','$2a$04$iP5Tly1tbOql6xcQ2aQw0O/Rlye1yWayLcd1uuc6umdLVRlO9qD4y','Russia'),(10,'Toddie','Joinson','tjoinson9','james','Norway'),(13,'John','Doe','johndoeee123','$2y$10$QV9vvxrD5EsVNAR3k81BOe3slWPvQbMSAPicFWMLKF.j6a2hXPFOm','USA'),(16,'John','Doe','johndoeee123','$2y$10$WDeCZQn9ktHPPSO63z8PfutOcMOqoq0plfm4c.Z6kdQGx5Po0uRX.','USA');
/*!40000 ALTER TABLE `applicant` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applicant_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicant_key` (
  `id` int NOT NULL,
  `owner_id` int DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `applicant_key_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `applicant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicant_key` WRITE;
/*!40000 ALTER TABLE `applicant_key` DISABLE KEYS */;
INSERT INTO `applicant_key` VALUES (1,2,'awt_ab598017be4550411284958a4c2014ec56000457792e7b92307137b824a24d38_6ee865bfab165006ae13495fdcd6617c1c300c0b98aaa0b078e0ff52091a96ac',1);
/*!40000 ALTER TABLE `applicant_key` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applicant_key_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicant_key_permission` (
  `id` int NOT NULL,
  `key_id` int DEFAULT NULL,
  `permission_id` int DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_id` (`key_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `applicant_key_permission_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `applicant_key` (`id`),
  CONSTRAINT `applicant_key_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicant_key_permission` WRITE;
/*!40000 ALTER TABLE `applicant_key_permission` DISABLE KEYS */;
INSERT INTO `applicant_key_permission` VALUES (1,1,1,'GET',1),(2,1,1,'POST',1),(3,1,1,'PUT',1),(4,1,1,'DELETE',1),(5,1,3,'GET',1),(6,1,3,'POST',1),(7,1,3,'PUT',1),(8,1,3,'DELETE',1),(9,1,4,'GET',1),(10,1,4,'POST',1),(11,1,4,'PUT',1),(12,1,4,'DELETE',1),(13,1,5,'GET',1),(14,1,5,'POST',1),(15,1,5,'PUT',1),(16,1,5,'DELETE',1),(17,1,6,'GET',1),(18,1,6,'POST',1),(19,1,6,'PUT',1),(20,1,6,'DELETE',1),(21,1,7,'GET',1),(22,1,7,'PUT',1),(23,1,7,'POST',1),(24,1,7,'DELETE',1),(25,1,8,'GET',1),(26,1,8,'POST',1),(27,1,8,'PUT',1),(28,1,8,'DELETE',1),(29,1,9,'GET',1),(30,1,9,'POST',1),(31,1,9,'PUT',1),(32,1,9,'DELETE',1),(33,1,10,'GET',1),(34,1,10,'POST',1),(35,1,10,'PUT',1),(36,1,10,'DELETE',1),(37,1,11,'GET',1),(38,1,11,'POST',1),(39,1,11,'PUT',1),(40,1,11,'DELETE',1),(41,1,12,'GET',1);
/*!40000 ALTER TABLE `applicant_key_permission` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applicantcontactinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicantcontactinfo` (
  `Form_ID` int DEFAULT NULL,
  `HomePhone` varchar(255) DEFAULT NULL,
  `WorkPhone` varchar(255) DEFAULT NULL,
  `CellPhone` varchar(255) DEFAULT NULL,
  `Other` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  UNIQUE KEY `applicantcontactinfo_UN` (`Form_ID`),
  CONSTRAINT `applicantcontactinfo_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicantcontactinfo` WRITE;
/*!40000 ALTER TABLE `applicantcontactinfo` DISABLE KEYS */;
INSERT INTO `applicantcontactinfo` VALUES (1,'555-123-4567','555-987-6543','555-555-5555','Some other contact information','example@example.com');
/*!40000 ALTER TABLE `applicantcontactinfo` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applicantfinancialprofile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicantfinancialprofile` (
  `Form_ID` int DEFAULT NULL,
  `living_status` varchar(255) DEFAULT NULL,
  `at_currently_address_yrs` int DEFAULT NULL,
  `at_currently_address_months` int DEFAULT NULL,
  `residents_in_home` int DEFAULT NULL,
  `dependents` int DEFAULT NULL,
  `employment_status` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `source_of_income` varchar(255) DEFAULT NULL,
  `income_amount` int DEFAULT NULL,
  `monthly_income` int DEFAULT NULL,
  `total_household_income_annually` int DEFAULT NULL,
  UNIQUE KEY `applicantfinancialprofile_UN` (`Form_ID`),
  CONSTRAINT `applicantfinancialprofile_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicantfinancialprofile` WRITE;
/*!40000 ALTER TABLE `applicantfinancialprofile` DISABLE KEYS */;
INSERT INTO `applicantfinancialprofile` VALUES (1,'Rent',2,8,4,3,'Self-Employed','Teacher','Business',75000,6000,90000);
/*!40000 ALTER TABLE `applicantfinancialprofile` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applicantverification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicantverification` (
  `Form_ID` int DEFAULT NULL,
  `Proof_Of_Address` blob,
  `ID_Type` varchar(255) DEFAULT NULL,
  `ID_Number` int DEFAULT NULL,
  `ID_Pic` blob,
  UNIQUE KEY `applicantverification_UN` (`Form_ID`),
  CONSTRAINT `applicantverification_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applicantverification` WRITE;
/*!40000 ALTER TABLE `applicantverification` DISABLE KEYS */;
INSERT INTO `applicantverification` VALUES (1,_binary 'Base64EncodedBlobData1','Passport',456789,_binary 'Base64EncodedBlobData2');
/*!40000 ALTER TABLE `applicantverification` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `ApplicantID` int DEFAULT NULL,
  `Form_ID` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `city_town_village` varchar(255) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Age` int DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `PEP_status` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`Form_ID`),
  KEY `ApplicantID` (`ApplicantID`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`ApplicantID`) REFERENCES `applicant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (1,1,'Hipolito Bautista','2 baboon avenue','Tiger Town','Belize','Cayo','Belmopan','2002-09-17',21,'Belizean',1,'2020-09-17','2024-09-17'),(2,2,'ronan orion','123 Main Street','Suburbia','USA','Downtown','Smallville','1990-01-01',30,'American',1,'2023-01-01','2023-12-31');
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `futureacademicdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `futureacademicdetails` (
  `Form_ID` int DEFAULT NULL,
  `Degree_Type` varchar(255) DEFAULT NULL,
  `Major` varchar(255) DEFAULT NULL,
  `Major_Duration` varchar(255) DEFAULT NULL,
  `Financing_Start_Date` date DEFAULT NULL,
  `Financing_End_Date` date DEFAULT NULL,
  `Institution_Name` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  UNIQUE KEY `futureacademicdetails_UN` (`Form_ID`),
  CONSTRAINT `futureacademicdetails_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `futureacademicdetails` WRITE;
/*!40000 ALTER TABLE `futureacademicdetails` DISABLE KEYS */;
INSERT INTO `futureacademicdetails` VALUES (1,'Bachelor of Science','Computer Science','4 years','2020-09-01','2024-05-30','University of Dummyville','Dummyville, USA');
/*!40000 ALTER TABLE `futureacademicdetails` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `institution` (
  `Form_ID` int DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `City_Town_Village` varchar(255) DEFAULT NULL,
  UNIQUE KEY `institution_UN` (`Form_ID`),
  CONSTRAINT `institution_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institution` WRITE;
/*!40000 ALTER TABLE `institution` DISABLE KEYS */;
INSERT INTO `institution` VALUES (1,'4 university heights','Cayo','Belmopan');
/*!40000 ALTER TABLE `institution` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `pastacademicdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pastacademicdetails` (
  `Form_ID` int DEFAULT NULL,
  `Recent_Education` varchar(255) DEFAULT NULL,
  `Degree_Earned` varchar(255) DEFAULT NULL,
  `Qualification_Earned` varchar(255) DEFAULT NULL,
  UNIQUE KEY `pastacademicdetails_UN` (`Form_ID`),
  CONSTRAINT `pastacademicdetails_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pastacademicdetails` WRITE;
/*!40000 ALTER TABLE `pastacademicdetails` DISABLE KEYS */;
INSERT INTO `pastacademicdetails` VALUES (1,'Master\'s in Business Administration','Master of Business Administration','MBA in Finance');
/*!40000 ALTER TABLE `pastacademicdetails` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission` (
  `id` int NOT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `resource` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES (1,'applicants','/',1),(2,'applicants','/',1),(3,'applications','/',1),(4,'studycost','/',1),(5,'financialprofile','/',1),(6,'reference','/',1),(7,'applicantcontactinfo','/',1),(8,'institution','/',1),(9,'pastacademicdetails','/',1),(10,'futureacademicdetails','/',1),(11,'applicantverification','/',1),(12,'portal','/',1);
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reference` (
  `Form_ID` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `real_estate_collateral` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `reference_UN` (`Form_ID`),
  CONSTRAINT `reference_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reference` WRITE;
/*!40000 ALTER TABLE `reference` DISABLE KEYS */;
INSERT INTO `reference` VALUES (1,'John Doe','johndoe@example.com','123-456-7890','Friend',1);
/*!40000 ALTER TABLE `reference` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `studycost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studycost` (
  `Form_ID` int DEFAULT NULL,
  `P_Tuition_SchoolFee` int DEFAULT NULL,
  `P_Books_Supplies` int DEFAULT NULL,
  `P_Boarding_Lodging` int DEFAULT NULL,
  `P_Traveling` int DEFAULT NULL,
  `P_Expenses` int DEFAULT NULL,
  `P_total` int DEFAULT NULL,
  `Tuition_SchoolFee` int DEFAULT NULL,
  `Books_Supplies` int DEFAULT NULL,
  `Boarding_Lodging` int DEFAULT NULL,
  `Traveling` int DEFAULT NULL,
  `Expenses` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  UNIQUE KEY `studycost_UN` (`Form_ID`),
  CONSTRAINT `studycost_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `applications` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `studycost` WRITE;
/*!40000 ALTER TABLE `studycost` DISABLE KEYS */;
INSERT INTO `studycost` VALUES (1,200,220,240,260,290,2600,100,120,140,180,1000,2500),(2,5000,250,800,100,400,6950,4500,200,750,80,350,5900);
/*!40000 ALTER TABLE `studycost` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

