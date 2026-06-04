-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 10:33 AM
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
-- Database: `marius_webshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `placed_orders`
--

CREATE TABLE `placed_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `imgURL` text NOT NULL,
  `description` text NOT NULL,
  `category` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `imgURL`, `description`, `category`) VALUES
(1, 'JURA J8', 1899, '\"./img/machines/juraj8.png\"', 'Deze volautomatische koffiemachine combineert Zwitserse precisie met gebruiksgemak en bereidt met één druk op de knop alles van een krachtige espresso tot een verfijnde latte macchiato of verkoelende Cold Brew. Het elegante ontwerp in Piano Black past moeiteloos in elk interieur.', 'machines'),
(2, 'Nivona CafeRomatica 8107', 1299, '\"./img/machines/Nivona_CafeRomatica_550.png\"', 'Deze volautomatische koffiemachine heeft een kleurendisplay, een ECO-modus en is extra stil door de geluidstille werking. Je kunt de machine tevens aansluiten met Bluetooth verbinding op de Nivona-app.', 'machines'),
(3, 'Zassenhaus Koffiemolen Montevideo', 153, '\"./img/grinders/zassenhaus_montevideo.jpg\"', 'De Montevideo is een topmolen van Zassenhaus. De molen is gemaakt van zeer stijlvol olijfhout en heeft een natuurlijke uitstraling. De molen is zeer fraai afgewerkt en is uiteraard voorzien van het kwaliteitsmaalwerk van Zassenhaus. Geschikt om 50 gram koffie per keer te malen. ', 'grinders'),
(4, 'Zassenhaus Manaos', 67, '\"./img/grinders/zassenhaus_manaos.jpg\"', 'Maal je koffiebonen met de Manaos koffiemolen. De modern vormgegeven handmolen is gemaakt van geborsteld RVS en geschikt voor het malen van bonen voor o.a. slow coffee, cafetière en snelfilter. De molen kan 60 gram bonen per keer te malen en vangt de koffie op in een voorraadbak van acryl. De ergonomische vorm zorgt voor een goede grip en maakt het vasthouden tijdens het malen gemakkelijk. ', 'grinders'),
(5, 'Guji Highlands', 12, '\"./img/coffee/guji_highlands.jpg\"', 'Niet alleen het verhaal achter de Guji Highlands maakt de koffie het proberen waard, de smaak zeker ook. Deze koffiebonen zijn zacht, fruitig, volzoet en erg aromatisch. Proef het zoete van abrikoos, zoethout en een vleugje kandij. Ook ontdek je het frisse van grapefruit in deze koffie. Tot slot maakt de lange nasmaak deze koffie een échte aanrader. De smaakkarakteristieken van deze koffie zijn complex. Dat betekent dat er veel verschillende smaken zijn te herkennen in deze interessante koffie. Geniet van deze koffie uit Ethiopië, de bakermat van koffie! ', 'coffee'),
(6, 'Mocha Java', 14, '\"./img/coffee/mocha_java.jpg\"', 'Dit is een dark roast en een wereldse koffiemelange uit Guji (Ethiopië) en Java (Indonesië). Deze unieke blend van ongewassen koffies (natural) komt duidelijk terug in de smaak. De koffie is mooi in balans tussen zoet, fris en bitter, met een vol aroma, een rijke body en een lange nasmaak, waarin zoetheid uit een patisserie, rijpe fruittonen en een vleugje kersenlikeur te ontdekken zijn.', 'coffee');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `name` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`) VALUES
(1, 'marius@gmail.com', 'marius', '$2y$10$fu7uE7sB33Lc0ZOFNUZMuOkJms2xNt8SXn5jKBHyaosviQppF1EHe'),
(2, 'jeroen@hotmail.com', 'jeroen', '$2y$10$Ie0Gh/WH67BNmQcpDJ1nxeSdODdzJ4kXiEyAL1XqgksYeNLldY7au'),
(3, 'klaas@kpn.nl', 'klaas', '$2y$10$x2tLKepmuoV.nIzuS/NJ0Ogsju4M826016LOp2SXs26RXhObkqS/K');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `placed_orders`
--
ALTER TABLE `placed_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email` (`email`,`name`,`password`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `placed_orders`
--
ALTER TABLE `placed_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `placed_orders`
--
ALTER TABLE `placed_orders`
  ADD CONSTRAINT `placed_orders_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `placed_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
