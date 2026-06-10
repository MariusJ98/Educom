-- from: https://github.com/LucasMcL/15-sql_queries_02-chinook

--1. Provide a query showing Customers (just their full names, customer ID and country) who are not in the US.
    SELECT FirstName, LastName, CustomerId, Country from customer WHERE country<>'USA';

--2. Provide a query only showing the Customers from Brazil.
    SELECT FirstName, LastName, CustomerId, Country from customer WHERE country='Brazil';

--3. Provide a query showing the Invoices of customers who are from Brazil. 
--The resultant table should show the customer's full name, Invoice ID, Date of the invoice and billing country.
    SELECT customer.FirstName, 
            customer.LastName, 
            invoice.InvoiceID, 
            invoice.InvoiceDate, 
            invoice.BillingCountry 
            FROM customer 
            JOIN invoice ON customer.CustomerId = invoice.CustomerId
            WHERE Country='Brazil'

-- 4. Provide a query showing only the Employees who are Sales Agents.
    SELECT FirstName, LastName FROM employee WHERE Title LIKE '%sales%';

-- 5. Provide a query showing a unique list of billing countries from the Invoice table.
    SELECT distinct(BillingCountry) FROM invoice 

-- 6. Provide a query that shows the invoices associated with each sales agent. 
--The resultant table should include the Sales Agent's full name.
SELECT e.FirstName, 
        e.Lastname, 
        i.*
    FROM customer AS c
    JOIN employee AS e ON c.SupportRepId=e.EmployeeId
    JOIN invoice AS i ON c.CustomerId=i.CustomerId

-- 7. Provide a query that shows the Invoice Total, Customer name, Country and Sale Agent name 
    for all invoices and customers.
    SELECT i.Total, 
        c.FirstName AS CustomerFirstName, 
        c.LastName AS CustomerLastName, 
        c.Country, 
        e.FirstName AS AgentFirstName, 
        e.LastName As AgentLastName
    FROM customer AS c
    JOIN employee AS e ON c.SupportRepId=e.EmployeeId
    JOIN invoice AS i ON c.CustomerId=i.CustomerId

-- 8. How many Invoices were there in 2009 and 2011? 
--    What are the respective total sales for each of those years?

SELECT YEAR(InvoiceDate), 
COUNT(total) AS TotalInvoices,
SUM(total) AS SumOfInvoices
FROM invoice 
WHERE YEAR(InvoiceDate) IN ('2009', '2011') 
GROUP BY YEAR(InvoiceDate)

--Looking at the InvoiceLine table, provide a query that COUNTs the number of line items for Invoice ID 37.

SELECT  COUNT(InvoiceID) 
FROM invoiceline 
WHERE InvoiceId=37

--Looking at the InvoiceLine table, provide a query that COUNTs the number of line items for each Invoice. 

SELECT  InvoiceId, COUNT(InvoiceID) 
FROM invoiceline 
GROUP BY InvoiceId

-- Provide a query that includes the track name with each invoice line item.

SELECT track.Name, invoiceline.*
FROM invoiceline AS il
JOIN track ON il.Trackid=track.TrackId

-- Provide a query that includes the purchased track name AND artist name with each invoice line item.
SELECT track.Name, 
    artist.Name, 
    il.*
FROM invoiceline AS il
JOIN track ON track.TrackId=il.TrackId
JOIN album ON album.albumId=track.AlbumId
JOIN artist ON artist.ArtistId=album.ArtistId

-- Provide a query that shows the # of invoices per country. HINT: GROUP BY
SELECT BillingCountry,
    COUNT(BillingCountry) AS `#invoices per country`
FROM invoice
GROUP BY BillingCountry

-- Provide a query that shows the total number of tracks in each playlist. 
--The Playlist name should be include on the resultant table.
SELECT playlist.Name,
        COUNT(trackID) AS `number of tracks`
FROM playlisttrack
JOIN playlist ON playlist.PlaylistId=playlisttrack.PlaylistId
GROUP BY playlist.Name

--Provide a query that shows all the Tracks, but displays no IDs. 
--The resultant table should include the Album name, Media type and Genre.

SELECT track.Name, 
    album.Title, 
    mediatype.Name, 
    genre.Name
from track
JOIN album ON track.AlbumId=album.AlbumId
JOIN genre ON track.GenreId=genre.GenreId
Join mediatype ON track.MediaTypeId=mediatype.MediaTypeId

-- Provide a query that shows all Invoices but includes the # of invoice line items.
SELECT  invoice.InvoiceDate, invoice.CustomerId, invoice.BillingCity, COUNT(invoiceline.InvoiceId) 
FROM invoiceline 
JOIN invoice ON invoiceline.InvoiceId=invoice.InvoiceId
GROUP BY invoice.InvoiceId,invoice.InvoiceDate, invoice.CustomerId, invoice.BillingCity;

-- Provide a query that shows total sales made by each sales agent.
SELECT employee.FirstName, 
    employee.LastName, 
    sum(invoice.Total) AS `# of sales`
FROM customer
JOIN employee ON customer.SupportRepID=employee.EmployeeId
JOIN invoice ON invoice.CustomerId=customer.CustomerId
GROUP BY employee.FirstName, employee.LastName

-- Which sales agent made the most in sales in 2009?
SELECT YEAR(InvoiceDate), 
    employee.FirstName, 
    employee.LastName, 
    sum(invoice.Total) AS `# of sales`
FROM customer
JOIN employee ON customer.SupportRepID=employee.EmployeeId
JOIN invoice ON invoice.CustomerId=customer.CustomerId
WHERE YEAR(InvoiceDate) IN ('2009')
GROUP BY employee.FirstName, employee.LastName
ORDER BY sum(invoice.Total) DESC
LIMIT 1
 
-- Which sales agent made the most in sales in 2010? Jane Peacock
-- Which sales agent made the most in sales over all? jane peacock
Provide a query that shows the # of customers assigned to each sales agent.
Provide a query that shows the total sales per country. Which country's customers spent the most?
Provide a query that shows the most purchased track of 2013.
Provide a query that shows the top 5 most purchased tracks over all.
Provide a query that shows the top 3 best selling artists.
Provide a query that shows the most purchased Media Type.