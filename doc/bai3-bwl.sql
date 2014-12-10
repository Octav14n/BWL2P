-- phpMyAdmin SQL Dump
-- version 4.2.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. Dez 2014 um 10:50
-- Server Version: 5.5.40-0ubuntu1
-- PHP-Version: 5.5.12-2ubuntu4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `bai3-bwl`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Bauteil`
--

CREATE TABLE IF NOT EXISTS `Bauteil` (
`BauteilID` int(11) NOT NULL,
  `Preis` double NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `VorlaufTage` int(11) NOT NULL,
  `GewichtKG` double NOT NULL,
  `AufLager` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `Bauteil`
--

INSERT INTO `Bauteil` (`BauteilID`, `Preis`, `Name`, `VorlaufTage`, `GewichtKG`, `AufLager`) VALUES
(1, 10, 'Betriebssystem', 1, 1, 1),
(3, 3, 'KernelAPI', 20, 1, 6),
(4, 0.1, 'C Funktion', 1, 1, 400),
(5, 12, 'IPC', 12, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `BauteilUnterbauteil`
--

CREATE TABLE IF NOT EXISTS `BauteilUnterbauteil` (
`BauteilUnterbauteilID` int(11) NOT NULL,
  `BauteilID` int(11) NOT NULL,
  `UnterBauteilID` int(11) NOT NULL,
  `Menge` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `BauteilUnterbauteil`
--

INSERT INTO `BauteilUnterbauteil` (`BauteilUnterbauteilID`, `BauteilID`, `UnterBauteilID`, `Menge`) VALUES
(1, 1, 3, 4),
(2, 3, 4, 200),
(3, 5, 3, 2),
(5, 1, 5, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Kategorie`
--

CREATE TABLE IF NOT EXISTS `Kategorie` (
`KategorieID` int(11) NOT NULL,
  `UeberKategorieID` int(11) DEFAULT NULL,
  `Name` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `Kategorie`
--

INSERT INTO `Kategorie` (`KategorieID`, `UeberKategorieID`, `Name`) VALUES
(2, NULL, 'PC'),
(3, 2, 'Software'),
(4, 3, 'Betriebssysteme');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Kunde`
--

CREATE TABLE IF NOT EXISTS `Kunde` (
`KundeID` int(11) NOT NULL,
  `Nutzername` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Passwort` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Vorname` text COLLATE utf8_unicode_ci NOT NULL,
  `Nachname` text COLLATE utf8_unicode_ci NOT NULL,
  `Strasse` text COLLATE utf8_unicode_ci NOT NULL,
  `PLZ` int(11) NOT NULL,
  `Ort` text COLLATE utf8_unicode_ci NOT NULL,
  `EMail` text COLLATE utf8_unicode_ci NOT NULL,
  `Geschlecht` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `Kunde`
--

INSERT INTO `Kunde` (`KundeID`, `Nutzername`, `Passwort`, `Vorname`, `Nachname`, `Strasse`, `PLZ`, `Ort`, `EMail`, `Geschlecht`) VALUES
(1, 'test', '1234', 'Max', 'Mustermann', 'Musterstrasse 1', 19999, 'Musterort', 'Max.Mustermann@localhost', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Photo`
--

CREATE TABLE IF NOT EXISTS `Photo` (
`PhotoID` int(11) NOT NULL,
  `ProduktID` int(11) NOT NULL,
  `URI` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Beschreibung` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `Photo`
--

INSERT INTO `Photo` (`PhotoID`, `ProduktID`, `URI`, `Beschreibung`) VALUES
(1, 1, 'win1.jpg', 'Windows in seinem Element'),
(2, 1, 'win2.jpg', 'Jetzt auch mit Smiley'),
(3, 2, 'uni1.jpg', 'Ein einfaches Anwendungszenario'),
(4, 3, 'ipc1.jpg', 'IPC wenn es gut läuft');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Produkt`
--

CREATE TABLE IF NOT EXISTS `Produkt` (
`ProduktID` int(11) NOT NULL,
  `BauteilID` int(11) NOT NULL,
  `Name` text COLLATE utf8_unicode_ci NOT NULL,
  `Beschreibung` text COLLATE utf8_unicode_ci NOT NULL,
  `Preis` double NOT NULL,
  `KategorieID` int(11) NOT NULL,
  `GueltigAb` date NOT NULL,
  `GueltigBis` date NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `Produkt`
--

INSERT INTO `Produkt` (`ProduktID`, `BauteilID`, `Name`, `Beschreibung`, `Preis`, `KategorieID`, `GueltigAb`, `GueltigBis`) VALUES
(1, 1, 'Windows', 'Bluescreen À la carte, garniert mit Bluescreen.', 520, 4, '2014-11-01', '2014-11-30'),
(2, 1, 'Linux', 'Ein Tux frei haus.', 20, 4, '2014-11-01', '2014-11-30'),
(3, 5, 'IPC', 'Inter process comunication\r\n\r\nEine Schnittstelle durch die es Prozessen ermöglicht wird miteinander zu kommunizieren.', 120, 4, '0000-00-00', '3014-10-02');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Rechnung`
--

CREATE TABLE IF NOT EXISTS `Rechnung` (
`RechnungID` int(11) NOT NULL,
  `KundeID` int(11) NOT NULL,
  `IBAN` varchar(34) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `Rechnung`
--

INSERT INTO `Rechnung` (`RechnungID`, `KundeID`, `IBAN`) VALUES
(15, 1, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `RechnungWarenkorb`
--

CREATE TABLE IF NOT EXISTS `RechnungWarenkorb` (
`RechnungWarenkorbID` int(11) NOT NULL,
  `RechnungID` int(11) NOT NULL,
  `WarenkorbID` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `RechnungWarenkorb`
--

INSERT INTO `RechnungWarenkorb` (`RechnungWarenkorbID`, `RechnungID`, `WarenkorbID`) VALUES
(27, 15, 3),
(28, 15, 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Vorhersage`
--

CREATE TABLE IF NOT EXISTS `Vorhersage` (
`VorhersageID` int(11) NOT NULL,
  `BauteilID` int(11) NOT NULL,
  `Soll` int(11) NOT NULL,
  `Ist` int(11) DEFAULT NULL,
  `Zeitraum` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=58 ;

--
-- Daten für Tabelle `Vorhersage`
--

INSERT INTO `Vorhersage` (`VorhersageID`, `BauteilID`, `Soll`, `Ist`, `Zeitraum`) VALUES
(36, 1, 1, 1, 0),
(37, 3, 6, 6, 0),
(38, 4, 400, 400, 0),
(39, 5, 0, 0, 0),
(43, 1, 1, 1, 1),
(44, 3, 6, 6, 1),
(45, 4, 400, 400, 1),
(46, 5, 0, 0, 1),
(50, 1, 1, 0, 2),
(51, 3, 6, 0, 2),
(52, 4, 400, 0, 2),
(53, 5, 0, 0, 2),
(54, 1, 1, 0, 3),
(55, 3, 6, 0, 3),
(56, 4, 400, 0, 3),
(57, 5, 0, 0, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Warenkorb`
--

CREATE TABLE IF NOT EXISTS `Warenkorb` (
`WarenkorbID` int(11) NOT NULL,
  `KundenID` int(11) NOT NULL,
  `ProduktID` int(11) NOT NULL,
  `Menge` int(11) NOT NULL,
  `Bestelldatum` timestamp NULL DEFAULT NULL,
  `Versanddatum` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `Warenkorb`
--

INSERT INTO `Warenkorb` (`WarenkorbID`, `KundenID`, `ProduktID`, `Menge`, `Bestelldatum`, `Versanddatum`) VALUES
(3, 1, 2, 2, '2014-12-09 23:34:45', NULL),
(4, 1, 3, 1, '2014-12-09 23:34:45', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Bauteil`
--
ALTER TABLE `Bauteil`
 ADD PRIMARY KEY (`BauteilID`), ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `BauteilUnterbauteil`
--
ALTER TABLE `BauteilUnterbauteil`
 ADD PRIMARY KEY (`BauteilUnterbauteilID`), ADD KEY `BauteilID` (`BauteilID`,`UnterBauteilID`), ADD KEY `UnterBauteilID` (`UnterBauteilID`);

--
-- Indexes for table `Kategorie`
--
ALTER TABLE `Kategorie`
 ADD PRIMARY KEY (`KategorieID`), ADD KEY `UeberID` (`UeberKategorieID`), ADD KEY `UeberKategorieID` (`UeberKategorieID`);

--
-- Indexes for table `Kunde`
--
ALTER TABLE `Kunde`
 ADD PRIMARY KEY (`KundeID`);

--
-- Indexes for table `Photo`
--
ALTER TABLE `Photo`
 ADD PRIMARY KEY (`PhotoID`), ADD KEY `ProduktID` (`ProduktID`);

--
-- Indexes for table `Produkt`
--
ALTER TABLE `Produkt`
 ADD PRIMARY KEY (`ProduktID`), ADD KEY `KategorieID` (`KategorieID`), ADD KEY `BauteilID` (`BauteilID`);

--
-- Indexes for table `Rechnung`
--
ALTER TABLE `Rechnung`
 ADD PRIMARY KEY (`RechnungID`), ADD KEY `KundeID` (`KundeID`);

--
-- Indexes for table `RechnungWarenkorb`
--
ALTER TABLE `RechnungWarenkorb`
 ADD PRIMARY KEY (`RechnungWarenkorbID`), ADD KEY `RechnungID` (`RechnungID`,`WarenkorbID`), ADD KEY `RechnungID_2` (`RechnungID`), ADD KEY `WarenkorbID` (`WarenkorbID`);

--
-- Indexes for table `Vorhersage`
--
ALTER TABLE `Vorhersage`
 ADD PRIMARY KEY (`VorhersageID`), ADD KEY `BauteilID` (`BauteilID`);

--
-- Indexes for table `Warenkorb`
--
ALTER TABLE `Warenkorb`
 ADD PRIMARY KEY (`WarenkorbID`), ADD KEY `KundenID` (`KundenID`), ADD KEY `ProduktID` (`ProduktID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bauteil`
--
ALTER TABLE `Bauteil`
MODIFY `BauteilID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `BauteilUnterbauteil`
--
ALTER TABLE `BauteilUnterbauteil`
MODIFY `BauteilUnterbauteilID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Kategorie`
--
ALTER TABLE `Kategorie`
MODIFY `KategorieID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Kunde`
--
ALTER TABLE `Kunde`
MODIFY `KundeID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Photo`
--
ALTER TABLE `Photo`
MODIFY `PhotoID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Produkt`
--
ALTER TABLE `Produkt`
MODIFY `ProduktID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Rechnung`
--
ALTER TABLE `Rechnung`
MODIFY `RechnungID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `RechnungWarenkorb`
--
ALTER TABLE `RechnungWarenkorb`
MODIFY `RechnungWarenkorbID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `Vorhersage`
--
ALTER TABLE `Vorhersage`
MODIFY `VorhersageID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `Warenkorb`
--
ALTER TABLE `Warenkorb`
MODIFY `WarenkorbID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `BauteilUnterbauteil`
--
ALTER TABLE `BauteilUnterbauteil`
ADD CONSTRAINT `BauteilUnterbauteil_ibfk_1` FOREIGN KEY (`BauteilID`) REFERENCES `Bauteil` (`BauteilID`),
ADD CONSTRAINT `BauteilUnterbauteil_ibfk_2` FOREIGN KEY (`UnterBauteilID`) REFERENCES `Bauteil` (`BauteilID`);

--
-- Constraints der Tabelle `Kategorie`
--
ALTER TABLE `Kategorie`
ADD CONSTRAINT `Kategorie_ibfk_1` FOREIGN KEY (`UeberKategorieID`) REFERENCES `Kategorie` (`KategorieID`);

--
-- Constraints der Tabelle `Photo`
--
ALTER TABLE `Photo`
ADD CONSTRAINT `Photo_ibfk_1` FOREIGN KEY (`ProduktID`) REFERENCES `Produkt` (`ProduktID`);

--
-- Constraints der Tabelle `Produkt`
--
ALTER TABLE `Produkt`
ADD CONSTRAINT `Produkt_ibfk_1` FOREIGN KEY (`KategorieID`) REFERENCES `Kategorie` (`KategorieID`),
ADD CONSTRAINT `Produkt_ibfk_2` FOREIGN KEY (`BauteilID`) REFERENCES `Bauteil` (`BauteilID`);

--
-- Constraints der Tabelle `Rechnung`
--
ALTER TABLE `Rechnung`
ADD CONSTRAINT `Rechnung_ibfk_1` FOREIGN KEY (`KundeID`) REFERENCES `Kunde` (`KundeID`);

--
-- Constraints der Tabelle `RechnungWarenkorb`
--
ALTER TABLE `RechnungWarenkorb`
ADD CONSTRAINT `RechnungWarenkorb_ibfk_1` FOREIGN KEY (`RechnungID`) REFERENCES `Rechnung` (`RechnungID`),
ADD CONSTRAINT `RechnungWarenkorb_ibfk_2` FOREIGN KEY (`WarenkorbID`) REFERENCES `Warenkorb` (`WarenkorbID`);

--
-- Constraints der Tabelle `Vorhersage`
--
ALTER TABLE `Vorhersage`
ADD CONSTRAINT `Vorhersage_ibfk_1` FOREIGN KEY (`BauteilID`) REFERENCES `Bauteil` (`BauteilID`);

--
-- Constraints der Tabelle `Warenkorb`
--
ALTER TABLE `Warenkorb`
ADD CONSTRAINT `Warenkorb_ibfk_1` FOREIGN KEY (`KundenID`) REFERENCES `Kunde` (`KundeID`),
ADD CONSTRAINT `Warenkorb_ibfk_2` FOREIGN KEY (`ProduktID`) REFERENCES `Produkt` (`ProduktID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
