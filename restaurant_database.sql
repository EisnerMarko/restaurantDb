CREATE DATABASE IF NOT EXISTS RestaurantDatabase;
USE RestaurantDatabase;

--reset to be able rerun this scrip

DROP TABLE IF EXISTS Booking;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS RestaurantTable;

--table creations
--engine def. cause of error

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

--inserts

INSERT INTO Customer
    (CustomerID, FirstName, LastName, Email, Phone)
VALUES
    (1, 'Emma', 'Jensen',
     'emma.jensen@example.com', '+45 20123456'),
    (2, 'Oliver', 'Nielsen',
     'oliver.nielsen@example.com', '+45 22123456'),
    (3, 'Sofia', 'Andersen',
     'sofia.andersen@example.com', '+45 30123456'),
    (4, 'William', 'Larsen',
     'william.larsen@example.com', '+45 40123456'),
    (5, 'Freja', 'Hansen',
     'freja.hansen@example.com', '+45 50123456');

INSERT INTO RestaurantTable
    (TableID, TableNumber, Capacity, Location)
VALUES
    (1, 1, 2, 'Window'),
    (2, 2, 4, 'Window'),
    (3, 3, 4, 'Main dining area'),
    (4, 4, 6, 'Main dining area'),
    (5, 5, 8, 'Private dining area'),
    (6, 6, 2, 'Terrace');
    
INSERT INTO Booking
    (BookingID, CustomerID, TableID,
     BookingDateTime, NumberOfGuests)
VALUES
    (1, 1, 2, '2026-10-12 18:00:00', 3),
    (2, 2, 1, '2026-10-12 18:30:00', 2),
    (3, 3, 4, '2026-10-12 19:00:00', 5),
    (4, 1, 3, '2026-10-14 19:30:00', 4),
    (5, 4, 5, '2026-10-12 20:00:00', 6),
    (6, 5, 2, '2026-10-12 19:00:00', 2),
    (7, 2, 6, '2026-10-15 18:00:00', 2),
    (8, 3, 1, '2026-10-14 18:30:00', 2);