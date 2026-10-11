-- query 1
USE RestaurantDatabase;

SELECT
    TableID,
    TableNumber,
    Capacity,
    Location
FROM RestaurantTable
ORDER BY TableNumber;

-- query 2

SELECT
    b.BookingID,
    b.BookingDateTime,
    b.NumberOfGuests,
    rt.TableNumber,
    rt.Location
FROM Booking AS b
JOIN RestaurantTable AS rt
    ON b.TableID = rt.TableID
WHERE b.CustomerID = 1
ORDER BY b.BookingDateTime ASC;

-- query 3

SELECT
    b.BookingID,
    b.BookingDateTime,
    b.NumberOfGuests,
    rt.TableID,
    rt.TableNumber,
    c.CustomerID,
    c.FirstName,
    c.LastName,
    c.Email,
    c.Phone
FROM Booking AS b
JOIN RestaurantTable AS rt
    ON b.TableID = rt.TableID
JOIN Customer AS c
    ON b.CustomerID = c.CustomerID
WHERE b.TableID = 2
  AND b.BookingDateTime >= '2026-10-12 00:00:00'
  AND b.BookingDateTime < '2026-10-13 00:00:00'
ORDER BY b.BookingDateTime ASC;