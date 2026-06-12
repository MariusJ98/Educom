-- Opgave 3.1.1
SELECT * FROM mhl_cities;

-- opgave 3.1.2
SELECT distinct(name) FROM mhl_cities;

-- opgave 3.1.3
SELECT name, straat, huisnr, postcode FROM mhl_suppliers;

-- opgave 3.2.1
SELECT name, straat, huisnr, postcode FROM mhl_suppliers WHERE city_id = 104;

-- opgave 3.2.2
SELECT name, 
        straat, 
        huisnr, 
        postcode 
    FROM mhl_suppliers 
    WHERE membertype IN (1,2,3,8);

-- opgave 3.2.3
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers 
    WHERE city_id = 104 and p_city_id<>104 ;

-- opgave 3.2.4
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers 
    WHERE city_id = 104 OR city_id = 172;

-- opgave 3.2.5
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE huisnr BETWEEN 10 AND 20;

-- opgave 3.2.6
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE huisnr BETWEEN 11 AND 19 OR huisnr >100;

-- opgave 3.2.7
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE name LIKE "\'t%";

-- 3.2.8
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE name LIKE "%handel";

-- 3.2.9
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE name LIKE "%groothandel%";

-- opgave 3.2.10
SELECT name, 
    straat, 
    huisnr, 
    postcode 
    FROM mhl_suppliers
    WHERE name REGEXP "&.*;";

-- opgave 3.3.1
SELECT name FROM mhl_cities ORDER BY name;

-- opgave 3.3.2
SELECT * FROM mhl_suppliers ORDER BY membertype, city_id, postcode;

-- opgave 3.3.3
SELECT * FROM mhl_hitcount ORDER BY year ASC, month ASC, hitcount DESC;

-- opgave 4.1.1
-- amsterdam -> city_id:104, 
select mhl_suppliers.name, 
        mhl_suppliers.straat,
        mhl_suppliers.huisnr,
        mhl_suppliers.postcode
    FROM mhl_suppliers 
    WHERE city_id = 104;

-- opgave 4.1.2
select mhl_suppliers.name, 
        mhl_suppliers.straat,
        mhl_suppliers.huisnr,
        mhl_suppliers.postcode
        mhl_cities.name AS plaatsnaam
    FROM mhl_suppliers
    JOIN mhl_cities ON  mhl_suppliers.city_id=mhl_cities.ID
    WHERE city_id=104;

-- opgave 4.1.3
SELECT
    r.name,
    s.name, 
    s.straat, 
    s.huisnr, 
    s.postcode
FROM mhl_suppliers AS s
INNER JOIN mhl_cities AS c ON s.city_ID=c.ID
INNER JOIN mhl_suppliers_mhl_rubriek_view AS sr ON s.id=sr.mhl_suppliers_ID
INNER JOIN mhl_rubrieken AS r ON sr.mhl_rubriek_view_ID=r.id
WHERE c.name = 'Amsterdam' AND (r.name=''235'' OR r.parent=235)
ORDER BY r.name, s.name

-- opgave 4.1.4
-- display: naam, straat, huisnummer, postcode, 
-- leveranciers --> specialistische leverancier ook voor particulieren
-- eigenschappen

SELECT 
    s.name, 
    s.straat, 
    s.huisnr, 
    s.postcode
FROM mhl_yn_properties AS yn 
INNER JOIN mhl_suppliers AS s ON yn.supplier_ID=s.ID
INNER JOIN mhl_propertytypes AS pt ON yn.propertytype_ID=pt.ID
WHERE pt.name='ook voor particulieren' OR pt.name='specialistische leverancier'

-- opgave 4.1.5
-- display naam, straat, huisnr, postcode, lat, long
-- sort by lat(max)
-- limit 5
SELECT 
    s.name, 
    s.straat, 
    s.huisnr, 
    s.postcode,
    pc.lat,
    pc.lng
FROM mhl_suppliers AS s 
INNER JOIN pc_lat_long AS pc ON pc.pc6=s.postcode
ORDER BY pc.lat DESC
LIMIT 5

-- opgave 4.1.6
-- hitcount(jan 2014), naam, stad, gemeente, provincie(zeeland, brabant, limburg),
SELECT
    hc.hitcount,
    s.name,
    c.name,
    cm.name,
    d.name
FROM mhl_suppliers AS s 
INNER JOIN mhl_hitcount AS hc ON hc.supplier_ID=s.id AND hc.year=2014 AND hc.month=1
INNER JOIN mhl_cities AS c ON c.id=s.city_ID
INNER JOIN mhl_communes AS cm ON cm.id=c.commune_ID
INNER JOIN mhl_districts AS d ON d.id=cm.district_ID
WHERE d.name='Limburg' OR d.name='Noord-Brabant' OR d.name='Zeeland'

-- opgave 4.1.7
-- select: cities.name, cities.id, commune.name, commune.id
SELECT 
    C1.name, 
    C2.name, 
    C1.id, C2.id, 
    C1.commune_id, 
    C2.commune_id
FROM mhl_cities C1
JOIN mhl_cities AS C2 ON C1.name=C2.name
WHERE C1.id < C2.id
ORDER BY C1.name

-- 4.1.8
-- city, city_id,
SELECT   
    city1.name,
    city1.id AS city1id,
    city2.id AS city2id,
    gemeente1.id AS gemeente1id,
    gemeente2.id AS gemeente2id,
    gemeente1.name AS gemeente1naam,
    gemeente2.name AS gemeente2naam
FROM mhl_cities AS city1
JOIN mhl_cities AS ccity2 ON c1.name=c2.name
JOIN mhl_communes AS gemeente1 ON c1.commune_ID=g1.id
JOIN mhl_communes AS gemeente2 on c2.commune_ID=g2.ID
WHERE city1.id < city2.id
ORDER BY city1.name

-- 4.2.1
SELECT 
    c.name,
    g.ID
FROM mhl_cities AS c
LEFT JOIN mhl_communes AS g ON c.commune_ID=g.ID
WHERE ISNULL(g.name)

-- 4.2.2
SELECT
    c.name,
    g.name IFNULL(NULL, "Invalid")
FROM mhl_cities AS c1
LEFT JOIN mhl_communes AS g ON c.commune_ID=g.ID

-- 4.2.3 
-- hoofdrubriek (a-z), subrubriek (a->z)
SELECT  
    hr.id,
    IFNULL(hr.name, sr.name) AS hoofdrubriek,
    IF(ISNULL(hr.name), '', sr.name) AS subrubriek
FROM mhl_rubrieken AS hr
RIGHT OUTER JOIN mhl_rubrieken AS sr ON sr.parent=hr.id
ORDER BY hoofdrubriek, subrubriek;

-- 4.2.4
--selecteer alle mogelijke Y/Nproperties van leveranciers (Amsterdam)
-- Property type A, als aanwezig->property value

SELECT
    s.name,
    pr.name,
    IFNULL(ynp.content, "Invalid") as value

FROM mhl_suppliers AS s
CROSS JOIN mhl_propertytypes
LEFT JOIN mhl_yn_properties AS ynp ON s.id=ynp.supplier_ID
JOIN mhl_cities AS c ON s.cityID=c.id
WHERE c.name='Amsterdam' AND mhl_propertytypes.proptype='A'

-- 5.1.1
SELECT 
    COUNT(mhl_hitcount.hitcount) AS `aantal records`,
    MIN(mhl_hitcount.hitcount) AS `laagste hitcount`,
    MAX(mhl_hitcount.hitcount) AS `hoogste hitcount`,
    AVG(mhl_hitcount.hitcount) AS `gemiddelde hitcount`,
    SUM(mhl_hitcount.hitcount) AS `totale hitcount`
FROM mhl_hitcount

-- 5.1.2
SELECT 
    mhl_hitcount.year,
    COUNT(mhl_hitcount.hitcount) AS `aantal records`,
    MIN(mhl_hitcount.hitcount) AS `laagste hitcount`,
    MAX(mhl_hitcount.hitcount) AS `hoogste hitcount`,
    AVG(mhl_hitcount.hitcount) AS `gemiddelde hitcount`,
    SUM(mhl_hitcount.hitcount) AS `totale hitcount`
FROM mhl_hitcount
GROUP BY mhl_hitcount.year

-- 5.1.3
SELECT 
    mhl_hitcount.year,
    mhl_hitcount.month,
    COUNT(mhl_hitcount.hitcount) AS `aantal records`,
    MIN(mhl_hitcount.hitcount) AS `laagste hitcount`,
    MAX(mhl_hitcount.hitcount) AS `hoogste hitcount`,
    AVG(mhl_hitcount.hitcount) AS `gemiddelde hitcount`,
    SUM(mhl_hitcount.hitcount) AS `totale hitcount`
FROM mhl_hitcount
GROUP BY mhl_hitcount.year, mhl_hitcount.month

-- 5.1.4
SELECT
    s.name,
    SUM(hc.hitcount) AS totalhits,
    COUNT(hc.month) AS totalmonths,
    AVG(hc.hitcount) AS averagemonthly
FROM mhl_hitcount AS hc
JOIN mhl_suppliers AS s ON hc.supplier_ID=s.ID
GROUP BY s.name
HAVING totalhits > 100
ORDER BY averagemonthly DESC;

-- 5.2.1
-- name, aanhef(directie), adres, postcode, stad
-- Als postadres bestaat, anders vestigingsadres
SELECT
    s.name AS leverancier,
    IFNULL(mhl_contacts.name, "tav de directie") AS aanhef,
    IF(p_address<>'', p_address, straat) AS adres,
    IF(p_address<>'', p_postcode, postcode) AS postcode,
    IF(p_address<>'', C1.name, C2.name) AS stad,
    IF(p_address<>'', d1.name, d2.name) AS provincie
FROM mhl_suppliers AS s
LEFT JOIN mhl_contacts ON s.ID=mhl_contacts.supplier_ID AND mhl_contacts.department=3
LEFT JOIN mhl_cities AS c1 ON C1.id=s.city_ID
LEFT JOIN mhl_communes co1 ON co1.id=c1.commune_ID
LEFT JOIN mhl_districts AS d1 ON d1.id=co1_ID
LEFT JOIN mhl_cities AS C2 ON C2.ID=s.city_ID
LEFT JOIN mhl_communes AS co2 ON co2.id=c2.commune_ID
LEFT JOIN mhl_districts AS d2 ON d2.id=c2.district_ID
--WHERE postcode <> ''
ORDER BY provincie, stad, leverancier

-- 5.2.2
SELECT
    c.name AS stad,
    COUNT(IF(mt.name = 'Gold', 1, NULL)) AS Gold,
    COUNT(IF(mt.name = 'Silver', 1, NULL)) AS Silver,
    COUNT(IF(mt.name = 'Bronze', 1, NULL)) AS Bronze,
    COUNT(IF(mt.name NOT IN ('Gold', 'Silver', 'Bronze'), 1, NULL)) AS Other
FROM mhl_suppliers AS s
JOIN mhl_membertypes AS mt ON s.membertype=mt.id
join mhl_cities AS c ON s.city_ID=c.ID
GROUP BY city_ID
ORDER BY GOLD DESC, SILVER DESC, BRONZE DESC, Other DESC

-- 5.2.3
SELECT 
    hc.year,
    SUM(CASE WHEN month IN (1,2,3) THEN hitcount END) AS Q1,
    SUM(CASE WHEN month IN (4,5,6) THEN hitcount END) AS Q2,
    SUM(CASE WHEN month IN (7,8,9) THEN hitcount END) AS Q3,
    SUM(CASE WHEN month IN (10,11,12) THEN hitcount END) AS Q4,
    SUM(hc.hitcount)
FROM mhl_hitcount AS hc
GROUP BY year