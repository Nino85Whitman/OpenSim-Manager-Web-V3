-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Version du serveur: 5.1.63
-- Version de PHP: 5.3.2-1ubuntu4.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `OSMW`
--

-- --------------------------------------------------------

--
-- Structure de la table `conf`
--

CREATE TABLE IF NOT EXISTS `conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cheminAppli` varchar(50) NOT NULL,
  `destinataire` varchar(50) NOT NULL,
  `Autorized` int(1) NOT NULL,
  `NbAutorized` int(11) NOT NULL,
  `VersionOSMW` varchar(50) NOT NULL,
  `urlOSMW` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `conf`
--

INSERT INTO `conf` (`id`, `cheminAppli`, `destinataire`, `Autorized`, `NbAutorized`, `VersionOSMW`, `urlOSMW`) VALUES
(1, '/OSMW/', 'votre_email@gmail.com', 1, 4, 'V3.4', '');

-- --------------------------------------------------------


--
-- Structure de la table `moteurs`
--
CREATE TABLE IF NOT EXISTS `moteurs` (
  `osAutorise` tinyint(4) NOT NULL AUTO_INCREMENT,
  `id_os` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `DB_OS` varchar(50) NOT NULL,
  `hypergrid` varchar(100) NOT NULL,
  PRIMARY KEY (`osAutorise`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `moteurs`
--

INSERT INTO `moteurs` (`osAutorise`, `id_os`, `name`, `version`, `address`, `DB_OS`, `hypergrid`) VALUES
(1, 'Opensim_1', 'Opensim_1', 'Nino-Private-Land', '/home/fgagod/Opensim-0.7.1-Sim1/', 'BDD_Opensim1', 'hg.francogrid.org:80'),
(2, 'Opensim_2', 'Opensim_2', 'Nino-Public-Land', '/home/fgagod/Opensim-0.7.1-Sim2/', 'BDD_Opensim2', 'hg.francogrid.org:80'),
(3, 'Opensim_3', 'Opensim_3', 'NinoCity', '/home/fgagod/Opensim-0.7.1-Sim3/', 'BDD_Opensim3', 'hg.francogrid.org:80'),
(4, 'Opensim_4', 'Opensim_4', 'Parc Attraction', '/home/fgagod/Opensim-0.7.1-Sim4/', 'BDD_Opensim4', 'hg.francogrid.org:80'),
(16, 'Opensim_Beta', 'Opensim_Beta', 'Opensim FG-Beta', '/home/fgagod/opensim-0.7.5-rc1-source/bin/', 'BDD_Opensim_beta', 'hg.beta.francogrid.org:8002');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `firstname` varchar(15) NOT NULL,
  `lastname` varchar(15) NOT NULL,
  `pass` text NOT NULL,
  `privilege` int(11) NOT NULL,
  `osAutorise` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Contenu de la table `users`
--

INSERT INTO `users` (`firstname`, `lastname`, `pass`, `privilege`, `osAutorise`) VALUES
('root', 'root', 'dc76e9f0c0006e8f919e0c515c66dbba3982f785', 4, '');
