CREATE DATABASE IF NOT EXISTS RestaurantDatabase;
USE RestaurantDatabase;

DROP TABLE IF EXISTS Booking;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS RestaurantTable;

CREATE TABLE Customer (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Phone VARCHAR(25)
) ENGINE=InnoDB;

CREATE TABLE RestaurantTable (
    TableID INT AUTO_INCREMENT PRIMARY KEY,
    TableNumber INT NOT NULL UNIQUE,
    Capacity INT NOT NULL,
    Location VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Booking (
    BookingID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    TableID INT NOT NULL,
    BookingDateTime DATETIME NOT NULL,
    NumberOfGuests INT NOT NULL,

    FOREIGN KEY (CustomerID)
        REFERENCES Customer(CustomerID),

    FOREIGN KEY (TableID)
        REFERENCES RestaurantTable(TableID)
) ENGINE=InnoDB;