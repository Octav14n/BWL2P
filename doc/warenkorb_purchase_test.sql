DELETE FROM RechnungWarenkorb;
DELETE FROM Rechnung;
DELETE FROM Vorhersage;
UPDATE Warenkorb SET Bestelldatum = NULL;
UPDATE Bauteil SET AufLager = 0;
UPDATE Bauteil SET AufLager = 1 WHERE Name = 'Betriebssystem';
UPDATE Bauteil SET AufLager = 6 WHERE Name = 'KernelAPI';
UPDATE Bauteil SET AufLager = 400 WHERE Name = 'C Funktion';
INSERT INTO Vorhersage (BauteilID, Soll, Ist, Zeitraum) SELECT BauteilID, AufLager, AufLager, 0 FROM Bauteil;
INSERT INTO Vorhersage (BauteilID, Soll, Ist, Zeitraum) SELECT BauteilID, AufLager, AufLager, 1 FROM Bauteil;
/* Dies sind genau 2x Linux + 1x IPC. */
