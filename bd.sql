-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `arch_archivos`;
CREATE TABLE `arch_archivos` (
  `IDArchivo` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un archivo almacenado en metik bajo la estructura <dispositivo>/<lote>/<sublote>/<archivo>',
  `IDSublote` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla arch_sublotes correspondiente al sublote al que pertenece el archivo.\n',
  `IDPersona` char(36) DEFAULT NULL,
  `NumArchivo` int(11) DEFAULT NULL COMMENT 'Numero de archivo que le corresponde en el sublote.',
  `NombreOriginalArchivo` text DEFAULT NULL COMMENT 'Nombre original del archivo al momento de ser almacenado en Metik',
  `ExtensionArchivo` varchar(10) DEFAULT NULL COMMENT 'Extensión original del archivo y que define el tipo de archivo que se almacena.',
  `TamanoArchivo` int(11) DEFAULT NULL COMMENT 'Número de bytes que ocupa el archivo en el dispositivo de almacenamiento.',
  `IDArchivoPadreArchivo` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla arc_archivos que este archivo reemplazó. Permite conocer la historia de las diferentes versiones de un mismo archivo. Debe ser nulo cuando el registro corresponda a la última o única versión del archivo.',
  `IDObjetoPadreArchivo` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en las tablas de objetos con el que está asociado el archivo. Permite buscar todos los archivos que se relacionan con un mismo objeto, por ejemplo: todas las imágenes asociadas a un predio.',
  `IDPermisoVerArchivo` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_permisos que indica el permiso necesario para visualizar este archivo. Sólo las personas que pertenezcan a un perfil que cuente con este permiso podrán visualizar el archivo. Si el valor es nulo, todas las personas del mismo cliente podrán visualizarlo.',
  `IDPermisoAbrirArchivo` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_permisos que indica el permiso necesario para abrir este archivo. Sólo las personas que pertenezcan a un perfil que cuente con este permiso podrán abrir el archivo. Si el valor es nulo, todas las personas del mismo cliente podrán abrirlo. ',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.\n',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDArchivo`),
  KEY `ArchivoSublote_idx` (`IDSublote`),
  KEY `ArchivoPadre_idx` (`IDArchivoPadreArchivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `arch_dispositivos`;
CREATE TABLE `arch_dispositivos` (
  `IDDispositivo` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una ruta o URL hacia un espacio en el que el servidor almacene archivos bajo la estructura <dispositivo>/<lote>/<sublote>/<archivo>',
  `URLDispositivo` varchar(512) DEFAULT NULL COMMENT 'Ruta o Localizador Universal de Recursos (URL) del espacio en el que el servidor almacena los archivos.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDispositivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `arch_lotes`;
CREATE TABLE `arch_lotes` (
  `IDLote` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un lote de almacenamiento físico, bajo la estructura <dispositivo>/<lote>/<sublote>/<archivo>',
  `NumeroLote` int(11) DEFAULT NULL COMMENT 'Número que le corresponde al lote dentro de la carpeta del cliente.',
  `ContadorSublotesLote` int(11) DEFAULT NULL COMMENT 'Contador del último sublote en la carpeta de este lote.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.\n',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDLote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `arch_sublotes`;
CREATE TABLE `arch_sublotes` (
  `IDSublote` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un sublote de almacenamiento físico, bajo la estructura <dispositivo>/<lote>/<sublote>/<archivo>',
  `IDLote` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla arch_lote correspondiente al lote al que pertenece el sublote que representa este registro.\n',
  `NumeroSublote` int(11) DEFAULT NULL COMMENT 'Número que le corresponde al sublote dentro de la carpeta de lotes.',
  `ContadorArchivosSublote` int(11) DEFAULT NULL COMMENT 'Contador del último archivo en la carpeta de este sublote.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.\n',
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDSublote`),
  KEY `SubloteLote_idx` (`IDLote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_clientes`;
CREATE TABLE `conf_clientes` (
  `IDCliente` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica a un cliente de Metik, es decir una empresa, organización, o a un conjunto de usuarios individuales que comparten acceso a la misma instancia del software pero mantienen su propio entorno de datos, configuraciones, y recursos.',
  `NombreCliente` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres que identifican al cliente.',
  `RazonSocialCliente` varchar(256) DEFAULT NULL COMMENT 'En el caso de que el cliente sea una persona moral, el nombre completo de la sociedad, tal y como está registrada con las autoridades fiscales.',
  `rfc` varchar(45) DEFAULT NULL,
  `ContadorLotesCliente` int(11) DEFAULT NULL COMMENT 'Valor del último lote creado para almacenar los archivos del cliente.',
  `nombreRepresentante` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `telefono` varchar(45) DEFAULT NULL,
  `comentarios` longtext DEFAULT NULL,
  `fechaIngreso` varchar(45) DEFAULT NULL,
  `vencimientoContrato` varchar(45) DEFAULT NULL,
  `logo` longtext DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `estatus` tinyint(4) DEFAULT 1,
  `diasAviso` bigint(20) DEFAULT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  PRIMARY KEY (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_datos_generales`;
CREATE TABLE `conf_datos_generales` (
  `IDDatoGeneral` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un dato que los usuarios de un cliente capturan para complementar la información de un predio.',
  `IDPredio` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_predios correspondiente al predio al que corresponde el dato que se capturó.',
  `IDMetadatoGeneral` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_metadatos_generales correspondiente a la definición de una propiedad o atributo del predio',
  `ValorDatoGeneral` varchar(512) DEFAULT NULL COMMENT 'Texto de hasta 512 caracteres con la información específica que se asigna a una propiedad o atributo de un predio.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `IDEdificio` char(36) DEFAULT NULL,
  `IDNivel` char(36) DEFAULT NULL,
  `IDZona` char(36) DEFAULT NULL,
  PRIMARY KEY (`IDDatoGeneral`),
  KEY `DatosGeneralesPredio_idx` (`IDPredio`),
  KEY `DatosGeneralesMetadatos_idx` (`IDMetadatoGeneral`),
  KEY `FK_DatosGenerales_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DatosGenerales_Metadatos` FOREIGN KEY (`IDMetadatoGeneral`) REFERENCES `conf_metadatos_generales` (`IDMetadatoGeneral`),
  CONSTRAINT `FK_DatosGenerales_Predio` FOREIGN KEY (`IDPredio`) REFERENCES `conf_predios` (`IDPredio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_datos_zona`;
CREATE TABLE `conf_datos_zona` (
  `IDZona` char(36) NOT NULL,
  `IDMetadatoGeneral` char(36) NOT NULL,
  `IDValorDatoZona` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDZona`,`IDMetadatoGeneral`),
  KEY `FK_DatosZona_MetadatosGenerales_idx` (`IDMetadatoGeneral`),
  CONSTRAINT `FK_DatosZona_MetadatosGenerales` FOREIGN KEY (`IDMetadatoGeneral`) REFERENCES `conf_metadatos_generales` (`IDMetadatoGeneral`),
  CONSTRAINT `FK_DatosZona_Zona` FOREIGN KEY (`IDZona`) REFERENCES `conf_zonas` (`IDZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_edificios`;
CREATE TABLE `conf_edificios` (
  `IDEdificio` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica cualquier estructura permanente que se haya levantado sobre un predio. Un edificio o construcción es cualquier obra que esté anclada de manera fija al suelo y sean parte del predio y, por lo tanto, son considerados como parte del bien inmueble en su conjunto.',
  `IDPredio` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_predios correspondiente al predio en el que está ubicado el edificio o construcción.',
  `ClaveEdificio` varchar(20) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que utiliza el cliente para identificar una construcción dentro del predio',
  `NombreEdificio` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente el cliente para referirse a una construcción dentro del predio. \nPor ejemplo: El nombre o el número del edificio, el area administrativa que utiliza el edificio o la función operativa que se realiza en él.\nEste nombre se utilizará en las aplicaciones para seleccionar el edificio de entre la lista de todos los predios del cliente.',
  `DescripcionEdificio` text DEFAULT NULL COMMENT 'Un texto que describa la construcción, su función y características. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FormaEdificio` polygon DEFAULT NULL COMMENT 'Un conjunto de puntos que permiten marcar el contorno del edificio sobre la imagen que se asocie al predio en el campo IDImagenPlanoPredio de la tabla conf_predios',
  `IDImagenCorteEdificio` text DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen con un corte del edificio para identificar sus diferentes pisos o niveles.',
  `IDImagenEdificio` text DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos en que se asocia a una imagen del edificio visto por alguno de sus frentes o a vuelo de pájaro. Se sugiere utilizar una imagen muy comercial ya que esta imagen es la que se mostrará en el explorador y en la ficha técnica.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `color` varchar(45) DEFAULT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `IDTrazo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDEdificio`),
  KEY `EdificiosPredio_idx` (`IDPredio`),
  KEY `FK_Edificios_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_Edificios_Predio` FOREIGN KEY (`IDPredio`) REFERENCES `conf_predios` (`IDPredio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_estados`;
CREATE TABLE `conf_estados` (
  `IDEstado` char(36) NOT NULL DEFAULT (uuid()),
  `IDPais` char(36) NOT NULL,
  `ClaveEstado` varchar(20) NOT NULL,
  `AbreviadoEstado` varchar(10) NOT NULL COMMENT 'Nombre del estado abreviado a 2 caracteres de acuerdo con el catálogo de ',
  `NombreEstado` varchar(45) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEstado`),
  KEY `FK_Estado_Pais_idx` (`IDPais`),
  CONSTRAINT `FK_Estado_Pais` FOREIGN KEY (`IDPais`) REFERENCES `conf_paises` (`IDPais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_metadatos_generales`;
CREATE TABLE `conf_metadatos_generales` (
  `IDMetadatoGeneral` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una propiedad o atributo de un predio. Los metadatos permiten describir características adicionales a los datos principales, proporcionando contexto, clasificación, o detalles adicionales que facilitan su organización, búsqueda, y comprensión.',
  `IDTipoMetadato` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_tipo_metadato correspondiente tipo de valores puede almacenar el dato y cómo se pueden validar y manipular esos valores.',
  `NombreMetadatoGeneral` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que se utilizará como etiqueta para identificar un dato.',
  `DescripcionMetadatoGeneral` text DEFAULT NULL COMMENT 'Un texto que describa la naturaleza del valor que se debe capturar. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1,
  `Tipo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDMetadatoGeneral`),
  KEY `MetadatoTipo_idx` (`IDTipoMetadato`),
  KEY `FK_Metadatos_Clientes_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_modelos_administrativos`;
CREATE TABLE `conf_modelos_administrativos` (
  `IDModeloAdministrativo` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica la forma en la que se administra el predio.',
  `ClaveModeloAdministrativo` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que se utiliza para identificar la forma de administrar un predio.',
  `NombreModeloAdministrativo` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente para referirse a la forma en la que se administra un predio.',
  `DescripcionModeloAdministrativo` text DEFAULT NULL COMMENT 'Un texto que describa la forma en la que se administra el predio. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDModeloAdministrativo`),
  KEY `FK_Modelos_Clientes_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_municipios`;
CREATE TABLE `conf_municipios` (
  `IDMunicipio` char(36) NOT NULL DEFAULT (uuid()),
  `IDEstado` char(36) DEFAULT NULL,
  `ClaveMunicipio` varchar(20) DEFAULT NULL,
  `NombreMunicipio` varchar(255) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDMunicipio`),
  KEY `FK_Municipio_Estados_idx` (`IDEstado`),
  CONSTRAINT `FK_Municipio_Estados` FOREIGN KEY (`IDEstado`) REFERENCES `conf_estados` (`IDEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_nichos_negocio`;
CREATE TABLE `conf_nichos_negocio` (
  `IDNichoNegocio` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un segmento específico del mercado inmobiliario que está orientado hacia un grupo particular de clientes con necesidades, preferencias, o características distintivas.',
  `ClaveNichoNegocio` char(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que utiliza el cliente para identificar un nicho de negocio específico.',
  `NombreNichoNegocio` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente el cliente para referirse a un nicho de negocio específico.\nPor ejemplo: Propiedades de lujo, vivienda sostenible, vivienda ecológica, inmuebles comerciales, grandes superficies, desarrollos urbanos, etc...',
  `DescripcionNichoNegocio` text DEFAULT NULL COMMENT 'Un texto que describa el segmento específico del mercado en el que se puede ubicar el predio y como satisface de manera óptima las necesidades de ese grupo particular de clientes. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True o 1) o sólamente cuando la persona es parte de un cliente (EsPublico=False o 0)',
  PRIMARY KEY (`IDNichoNegocio`),
  KEY `FK_Nichos_Clientes_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_niveles`;
CREATE TABLE `conf_niveles` (
  `IDNivel` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica cada una de las divisiones horizontales en las que se organiza un edificio o construccion, comúnmente conocidas como pisos o plantas. Cada nivel corresponde a un plano horizontal en el edificio, y está delimitado por el suelo de ese nivel y el techo.',
  `IDEdificio` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_predios correspondiente al edificio cuyo nivel se describe en el registro.',
  `ClaveNivel` varchar(20) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que utiliza el cliente para identificar el piso o planta.\n',
  `NombreNivel` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente el cliente para referirse a un piso o planta:\nPor ejemplo: Planta baja, primer nivel, segundo nivel, tercer nivel, etc., sótano, entresuelo o mezzanine, etc.',
  `DescripcionNivel` text DEFAULT NULL COMMENT 'Un texto que describa el nivel, su función y características. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FormaNivel` polygon DEFAULT NULL COMMENT 'Un conjunto de puntos que permiten marcar el contorno del nivel sobre la imagen que se asocie al predio a través del campoIDImagenCorteEdificio de la tabla conf_predios.',
  `IDImagenPlanoNivel` char(36) DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen con un croquis o plano de la planta que permita identificar sus diferentes areas o zonas.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `color` varchar(45) DEFAULT NULL,
  `IDTrazo` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDNivel`),
  KEY `NivelesEdificios_idx` (`IDEdificio`),
  KEY `FK_Niveles_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_NivelesEdificios` FOREIGN KEY (`IDEdificio`) REFERENCES `conf_edificios` (`IDEdificio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_paises`;
CREATE TABLE `conf_paises` (
  `IDPais` char(36) NOT NULL DEFAULT (uuid()),
  `ClavePais` varchar(3) DEFAULT NULL COMMENT 'Clave del pais a tres caracteres de acuerdo con la norma ISO 3166/2',
  `ClaveCortaPais` varchar(2) DEFAULT NULL COMMENT 'Clave del pais a dos caracteres de acuerdo con la norma ISO 3166/2',
  `NombrePais` varchar(45) DEFAULT NULL COMMENT 'Nombre del pais de acuerdo con la norma ISO 3166/2',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDPais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_predios`;
CREATE TABLE `conf_predios` (
  `IDPredio` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un predio asociado a un cliente.\nUn predio es una propiedad o terreno, generalmente con delimitaciones específicas. Puede ser urbano o rural, y puede estar destinado a diferentes usos, como vivienda, agricultura, comercio, entre otros. El predio puede incluir construcciones o ser simplemente un terreno vacío.',
  `ClavePredio` varchar(20) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con un nombre corto, código o clave que utiliza el cliente para identificar un predio.',
  `NombrePredio` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente el cliente para referirse a un predio. \nPor ejemplo: el nombre de la plaza comercial, la calle y número del edificio o el nombre de la empresa que utiliza el predio.\nEste nombre se utilizará en las aplicaciones para seleccionar un predio de entre la lista de todos los predios del cliente.',
  `DescripcionPredio` text DEFAULT NULL COMMENT 'Un texto que describa al predio y sus características. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `GeolocalizacionPredio` varchar(45) DEFAULT NULL COMMENT 'Las coordenadas geográficas (Latitud y Longitud) separadas por una coma y que permiten ubicar al predio en un mapa.',
  `IDModeloAdministrativo` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_modelos_administrativos que define la forma en que se administra el predio.',
  `IDNichoNegocio` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_nichos_negocio que define a que grupo comercial pertenece el predio',
  `IDTipoPredio` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_tipos_predio que define el uso que se le da al predio, por ejemplo: Comercial, Habitacional, Parque, Oficinas, Mixto, etc.\n',
  `HorarioOperacionPredio` varchar(45) DEFAULT NULL COMMENT 'El horario de servicio en que el público puede acceder al predio.',
  `CallePredio` varchar(128) DEFAULT NULL COMMENT 'El nombre de la calle en la que está ubicado el predio',
  `NumeroExteriorPredio` varchar(45) DEFAULT NULL COMMENT 'El número que identifica el acceso al predio en la calle que se ubica.',
  `NumeroInteriorPredio` varchar(45) DEFAULT NULL COMMENT 'El identificador del predio cuando comparte el mismo número exterior con otros predios.',
  `CodigoPostalPredio` varchar(5) DEFAULT NULL COMMENT 'Al código postal en el que se ubica el predio de acuerdo con el catálogo de SEPOMEX.',
  `ColoniaPredio` varchar(128) DEFAULT NULL COMMENT 'La colonia o fraccionamiento en el que se ubica el predio.\n',
  `CiudadPredio` varchar(128) DEFAULT NULL COMMENT 'La población o ciudad en el que se ubica el predio',
  `MunicipioPredio` varchar(128) DEFAULT NULL COMMENT 'El municipio o alcaldía en el que se ubica el predio.',
  `EstadoPredio` varchar(128) DEFAULT NULL COMMENT 'La entidad federativa en el que se ubica el predio.\n',
  `IDMunicipio` char(36) DEFAULT NULL,
  `IDEstado` char(36) DEFAULT NULL,
  `IDPais` char(36) DEFAULT NULL,
  `PaisPredio` varchar(128) DEFAULT NULL COMMENT 'El país en el que se ubica el predio',
  `FormaPredio` polygon DEFAULT NULL COMMENT 'Un conjunto de puntos que permiten marcar el contorno del predio en un mapa.',
  `IDImagenVistaPredio` longtext DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos en que se debe asociar a una imagen del predio visto por alguno de sus frentes o a vuelo de pájaro. Se sugiere utilizar una imagen muy comercial ya que esta imagen es la que se mostrará en el explorador y en la ficha técnica.',
  `IDImagenLogoPredio` longtext DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen del ícono o logo que se utilice comercialmente para identificar el predio.',
  `IDImagenPlanoPredio` longtext DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen satelital del predio o un plano urbano que permita ubicar el predio y se utilizará para identificar las diferentes construcciones ubicadas en él.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.\n',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDPredio`),
  KEY `PredioModeloAdmin_idx` (`IDModeloAdministrativo`),
  KEY `PredioTipoPredio_idx` (`IDTipoPredio`),
  KEY `FK_Predio_Cliente_idx` (`IDCliente`),
  KEY `FK_Predio_NichoNegocio_idx` (`IDNichoNegocio`),
  KEY `FK_Predio_Paises_idx` (`IDPais`),
  KEY `FK_Predio_Estados_idx` (`IDEstado`),
  KEY `FK_Predio_Municipios_idx` (`IDMunicipio`),
  CONSTRAINT `FK_Predio_ModeloAdmin` FOREIGN KEY (`IDModeloAdministrativo`) REFERENCES `conf_modelos_administrativos` (`IDModeloAdministrativo`),
  CONSTRAINT `FK_Predio_TipoPredio` FOREIGN KEY (`IDTipoPredio`) REFERENCES `conf_tipos_predio` (`IDTipoPredio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_tipos_inmueble`;
CREATE TABLE `conf_tipos_inmueble` (
  `IDTipoInmueble` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica que el uso que se le da al predio.',
  `ClaveTipoInmueble` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con una clave que identifique el uso que tiene el predio.',
  `NombreTipoInmueble` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se identifica comunmente el uso que se le puede dar a un inmueble\nPor ejemplo: Comercial, Habitacional, Parque, Oficinas, Mixto',
  `DescripcionTipoInmueble` text DEFAULT NULL COMMENT 'Un texto que describa el uso que se le da a un predio. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoInmueble`),
  KEY `FK_TipoPredio_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_tipos_metadato`;
CREATE TABLE `conf_tipos_metadato` (
  `IDTipoMetadato` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un tipo de metadato.',
  `ClaveTipoMetadato` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con una clave que identifique el tipo de metadato.',
  `NombreTipoMetadato` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se identifica comunmente el metadato:\nPor ejemplo: Entero, texto, fecha, booleano, etc.',
  `DescripcionTipoMetadato` text DEFAULT NULL COMMENT 'Un texto que describa el tipo de metadato. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `ExpRegularTipoMetadato` varchar(128) DEFAULT NULL COMMENT 'Una secuencia de caracteres que define un patrón de búsqueda que permita validar si la sintaxis del valor que se capture para este tipo de metadato es correcta o no.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoMetadato`),
  KEY `FK_TipoMetadato_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_tipos_predio`;
CREATE TABLE `conf_tipos_predio` (
  `IDTipoPredio` char(36) NOT NULL DEFAULT 'uuid()' COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica que el uso que se le da al predio.',
  `ClaveTipoPredio` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con una clave que identifique el uso que tiene el predio.',
  `NombreTipoPredio` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se identifica comunmente el uso que se le puede dar a un predio.\nPor ejemplo: Comercial, Habitacional, Parque, Oficinas, Mixto',
  `DescripcionTipoPredio` text DEFAULT NULL COMMENT 'Un texto que describa el uso que se le da a un predio. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoPredio`),
  KEY `FK_TipoPredio_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_TipoPredio_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_tipos_zona`;
CREATE TABLE `conf_tipos_zona` (
  `IDTipoZona` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica que el uso que se le da a la zona.',
  `ClaveTipoZona` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con una clave que identifique el uso que tiene una zona.',
  `NombreTipoZona` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se identifica comunmente el uso que se le puede dar a un predio.\nPor ejemplo: Comercial, habitacional, oficinas, cine, boliche, restaurant, etc.',
  `DescripcionTipoZona` text DEFAULT NULL COMMENT 'Un texto que describa el uso que se le da a una zona. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDImagenTipoZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen con un ícono o imagen que represente a la zona. Esta imagen se utiliza como default cuando la zona no tiene imagen asociada.',
  `EsRentableTipoZona` tinyint(4) NOT NULL COMMENT 'Valor booleano que indica si la zona se renta (0 = no, 1= sí).\n',
  `EsHabitableTipoZona` tinyint(4) NOT NULL COMMENT 'Valor booleano que indica si la zona se utiliza para habitación u oficinas. (0 = no, 1 = sí).',
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creo el registro en la base de datos.',
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó por última vez la información del registo.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoZona`),
  KEY `FK_TipoZona_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_trazos`;
CREATE TABLE `conf_trazos` (
  `IDTrazo` char(36) NOT NULL,
  `json` text DEFAULT NULL,
  `IDPredio` char(36) DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL,
  `IDImagen` text DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDTrazo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_uso_zona`;
CREATE TABLE `conf_uso_zona` (
  `IDUsoZona` char(36) NOT NULL,
  `NombreUsoZona` varchar(45) DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDUsoZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `conf_zonas`;
CREATE TABLE `conf_zonas` (
  `IDZona` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un área o subdivisión específica dentro de una planta de un edifici, que se destina a una función particular o se delimita por ciertas características arquitectónicas o funcionales.',
  `IDNivel` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_niveles correspondiente al nivel cuys zona se describe en el registro.',
  `IDUsoZona` char(36) DEFAULT NULL,
  `IDTipoZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_tipos_zona que',
  `ClaveZona` varchar(20) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que utiliza el cliente para identificar la zona.',
  `NombreZona` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que utiliza comunmente el cliente para referirse a una zona, por ejemplo: Área común, zonas de trabajo dentro de las oficinas o plantas industriales, oficinas individuales, salas de reuniones, áreas de producción, laboratorios, zonas de servicio o dedicadas a instalaciones y servicios esenciales, como baños, cocinas, cuartos de máquinas, o áreas de mantenimiento, areas de de circulación como corredores, escaleras, y ascensores. etc.',
  `DescripcionZona` text DEFAULT NULL COMMENT 'Un texto que describa la zona, su función y características. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FormaZona` polygon DEFAULT NULL COMMENT 'Un conjunto de puntos que permiten marcar el contorno de la zona sobre la imagen que se asocie a la zona a través del campo IDImagenPlanoNivel de la tabla conf_niveles',
  `SuperficieZona` float DEFAULT NULL COMMENT 'Superficie en metros cuadrados que ocupa la zona.',
  `CentroCostosZona` varchar(45) DEFAULT NULL COMMENT 'Número que se utiliza para identificar la cuenta contable que le corresponde al centro de costos de la zona.',
  `OcupadoZona` tinyint(4) DEFAULT NULL COMMENT 'Valor booleano que indica si la zona está ocupada (-1) o vacante (0)',
  `IDImagenZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen que muestre la vista de la zona a nivel de piso.',
  `IDImagenLogoZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave al registro en la tabla arch_archivos que se debe asociar a una imagen con un ícono o logo de la zona.',
  `IDTrazo` char(36) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el registro por última vez.\n',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDZona`),
  KEY `ZonasNivel_idx` (`IDNivel`),
  KEY `ZonasTipoZonas_idx` (`IDTipoZona`),
  KEY `FK_Zona_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Zona_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `IDFollow` char(36) NOT NULL,
  `IDObjeto` char(36) DEFAULT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDFollow`),
  UNIQUE KEY `IDFollow_UNIQUE` (`IDFollow`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_categorias_doc`;
CREATE TABLE `gd_categorias_doc` (
  `IDCategoriaDoc` char(36) NOT NULL DEFAULT (uuid()),
  `IDGrupoDoc` char(36) DEFAULT NULL,
  `ClaveCategoriaDoc` varchar(20) NOT NULL,
  `NombreCategoriaDoc` varchar(45) NOT NULL,
  `DescripcionCategoriaDoc` text DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `IDTipoInmueble` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDCategoriaDoc`),
  KEY `FK_CategoriaDoc_Clientes_idx` (`IDCliente`),
  KEY `FK_CategoriaDoc_GrupoDoc_idx` (`IDGrupoDoc`),
  CONSTRAINT `FK_CategoriaDoc_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_CategoriaDoc_GrupoDoc` FOREIGN KEY (`IDGrupoDoc`) REFERENCES `gd_grupos_doc` (`IDGrupoDoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_conceptos_serviciopublico`;
CREATE TABLE `gd_conceptos_serviciopublico` (
  `IDConceptoServicioPublico` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un concepto dentro de un servicio público, por ejemplo el suministro eléctrico dentro del servicio públco que proporciona la CFE',
  `IDServicioPublico` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla gd_servicios_publicos correspondiente al servicio al que pertenece el concepto que se describe en el registro\n',
  `IDTipoConceptoServicio` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla gd_tipos_conceptoservicio correspondiente al identificador del tipo de concepto al que pertenece el registro.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el registro por última vez.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDConceptoServicioPublico`),
  KEY `ConceptosServicioCatConcepto_idx` (`IDTipoConceptoServicio`),
  KEY `ConceptosServicioCatServicios_idx` (`IDServicioPublico`),
  KEY `FK_ConceptoServicio_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_ConceptoServicio_CatConceptos` FOREIGN KEY (`IDTipoConceptoServicio`) REFERENCES `gd_tipos_conceptoservicio` (`IDTipoConceptoServicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_consumos_cfe`;
CREATE TABLE `gd_consumos_cfe` (
  `IDConsumoCFE` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una factura o recibo del servicio expedido por la CFE',
  `IDServicioPublico` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla gd_servicios_publicos correspondiente al servicio al que pertenece el concepto que se describe en el registro\n',
  `IDConceptoServicioPublico` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla gd_conceptos_serviciopublico correspondiente al concepto de pago al que pertenece la factura o recibo que se describe en el registro\n',
  `RPUConsumoCFE` varchar(45) NOT NULL COMMENT 'RPU significa Registro Permanente de Usuario. Es un número que identifica de manera única al usuario y al servicio que se le brinda, y que no se repite en toda la República. \n',
  `ALecturaConsumoCFE` int(11) DEFAULT NULL COMMENT 'Última lectura del servicio',
  `PeriodoLecturaConsumoCFE` varchar(45) DEFAULT NULL COMMENT 'Período al que corresponde la factura o recibo.',
  `InicioPeriodoLecturaConsumoCFE` date DEFAULT NULL COMMENT 'Fecha en la que se hizo la lectura inicial del medidor.',
  `FinPeriodoLecturaConsumoCFE` date DEFAULT NULL,
  `LecturaAnteriorConsumoCFE` int(11) DEFAULT NULL COMMENT 'Lectura correspondiente al período anterior.\n',
  `LecturaActualConsumoCFE` int(11) DEFAULT NULL COMMENT 'Lectura correspondiente al período actual.',
  `DemandaConsumoCFE` double DEFAULT NULL,
  `ReactivoConsumoCFE` double DEFAULT NULL,
  `FactorPotenciaConsumoCFE` double DEFAULT NULL,
  `FactorCargaConsumoCFE` double DEFAULT NULL,
  `ImporteEnergiaConsumoCFE` double DEFAULT NULL,
  `ImporteDAPConsumoCFE` double DEFAULT NULL,
  `ImporteIVAConsumoCFE` double DEFAULT NULL,
  `ImporteTotalConsumoCFE` double DEFAULT NULL,
  `TasaIVAConsumoCFE` double DEFAULT NULL,
  `DocumentoIDConsumoCFE` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDConsumoCFE`),
  KEY `CFEServicioPublico_idx` (`IDServicioPublico`),
  KEY `CFEConceptoServicio_idx` (`IDConceptoServicioPublico`),
  KEY `CFERPU` (`RPUConsumoCFE`),
  KEY `FK_CFE_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_CFE_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_datos_documento`;
CREATE TABLE `gd_datos_documento` (
  `IDDatoDocumento` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica la captura de información adicional relacionada a un documento',
  `IDDocumento` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla gd_documentos correspondiente al documento al que se está agregando información complementaria',
  `MetadatoTipoDocumentoID` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla gd_metadatos_tipodocumento correspondiente al metadato del que se está capturando información en el registro',
  `ValorDatoDocumento` varchar(128) NOT NULL COMMENT 'La información complementara específica para el metadato del documento relacionado con este registro.',
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDDatoDocumento`),
  KEY `documento_idx` (`IDDocumento`),
  KEY `MetadatoDocumento_idx` (`MetadatoTipoDocumentoID`),
  KEY `FK_DatosDoc_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_DatosDoc_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_DatosDoc_Documento` FOREIGN KEY (`IDDocumento`) REFERENCES `gd_documentos` (`IDDocumento`),
  CONSTRAINT `FK_DatosDoc_MetadatoDocumento` FOREIGN KEY (`MetadatoTipoDocumentoID`) REFERENCES `gd_metadatos_tipodocumento` (`IDMetadatoTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_documentos`;
CREATE TABLE `gd_documentos` (
  `IDDocumento` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un documento específico del archivo documental.',
  `IDPredio` char(36) NOT NULL,
  `IDEdificio` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_edificios correspondiente al edificio en el que se encuentra la zona a la que pertenece el documento que se describe en este registro.\nCuando el documento corresponda al expediente del predio, esta columna deberá ser nula.',
  `IDNivel` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_niveles correspondiente al nivel de la zona a la que pertenece el documento que se describe en este registro.\nCuando el documento corresponda al expediente del predio, esta columna deberá ser nula.',
  `IDZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_zonas correspondiente a la zona a la que pertenece el documento que se describe en este registro.\nCuando el documento corresponda al expediente del predio, esta columna deberá ser nula.',
  `IDContratoZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ga_contratos correspondiente al contrato de la zona a la que pertenece el documento que se describe en este registro.\nCuando el documento corresponda al expediente del predio, esta columna deberá ser nula.',
  `IDTipoDocumento` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_tipos_documento correspondiente a la clasificación documental.',
  `DescripcionDocumento` text DEFAULT NULL COMMENT 'Un texto que describa características específicas del documento adicionales a las del tipo documental al que pertenece.',
  `FechaAlertaDocumento` date DEFAULT NULL COMMENT 'Fecha y hora en la que se transita el documento a un estado de alerta que indica que su vencimiento está cercano y deben inicarse las acciones de renovación.',
  `VenceDocumento` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que indica si el documento puede vencer. (-1= Sí, 0=No)',
  `FechaVencimientoDocumento` date DEFAULT NULL COMMENT 'Fecha y hora en la que se transita el documento a un estado que indica que ya no está vigente.',
  `FechaEmisionDocumento` date DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDocumento`),
  KEY `DocumentoPredio_idx` (`IDPredio`),
  KEY `DocumentoEdificio_idx` (`IDEdificio`),
  KEY `DocumentoNivel_idx` (`IDNivel`),
  KEY `DocumentoZona_idx` (`IDZona`),
  KEY `DocumentoTipoDocumento_idx` (`IDTipoDocumento`),
  KEY `FK_Documento_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Documento_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_Documento_Instancia` FOREIGN KEY (`IDDocumento`) REFERENCES `track_instancias` (`IDInstancia`),
  CONSTRAINT `FK_Documento_Predio` FOREIGN KEY (`IDPredio`) REFERENCES `conf_predios` (`IDPredio`),
  CONSTRAINT `FK_Documento_Zona` FOREIGN KEY (`IDZona`) REFERENCES `conf_zonas` (`IDZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_documentos_ejemplo`;
CREATE TABLE `gd_documentos_ejemplo` (
  `IDDocumento` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un documento específico del archivo documental.',
  `IDTipoDocumento` varchar(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_tipos_documento correspondiente a la clasificación documental.',
  `NombreDocumento` varchar(45) NOT NULL COMMENT 'Un texto que describa características específicas del documento adicionales a las del tipo documental al que pertenece.',
  `FechaAlertaDocumento` date NOT NULL COMMENT 'Fecha y hora en la que se transita el documento a un estado de alerta que indica que su vencimiento está cercano y deben inicarse las acciones de renovación.',
  `FechaVencimientoDocumento` date NOT NULL COMMENT 'Fecha y hora en la que se transita el documento a un estado que indica que ya no está vigente.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDocumento`),
  KEY `FK_documentoejemplo_cliente_idx` (`IDCliente`),
  KEY `FK_documentoejemplo_tipodocumento_idx` (`IDTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_grupos_doc`;
CREATE TABLE `gd_grupos_doc` (
  `IDGrupoDoc` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveGrupoDoc` varchar(20) NOT NULL,
  `NombreGrupoDoc` varchar(45) NOT NULL,
  `DescripcionGrupoDoc` text DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `IDTipoInmueble` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDGrupoDoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_metadatos_tipodocumento`;
CREATE TABLE `gd_metadatos_tipodocumento` (
  `IDMetadatoTipoDocumento` char(36) NOT NULL DEFAULT (uuid()),
  `IDTipoDocumento` char(36) NOT NULL,
  `ClaveMetadatoTipoDocumento` varchar(20) NOT NULL,
  `NombreMetadatoTipoDocumento` varchar(45) NOT NULL,
  `DescripcionMetadatoTipoDocumento` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  `EsMultiple` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDMetadatoTipoDocumento`),
  KEY `TipoDocumento_idx` (`IDTipoDocumento`),
  KEY `FK_Metadato_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Metadato_TipoDocumento` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_notificaciones`;
CREATE TABLE `gd_notificaciones` (
  `IDTipoDocumento` char(36) NOT NULL,
  `IDRol` char(36) NOT NULL,
  `FechaActualizacionObjeto` date DEFAULT NULL,
  `FechaCreacionObjeto` date DEFAULT NULL,
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDTipoDocumento`,`IDRol`),
  KEY `FK_RolTipo_Roles_idx` (`IDRol`),
  CONSTRAINT `FK_Roles_TipoDocumento` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_obligatorios_tipo_inmueble`;
CREATE TABLE `gd_obligatorios_tipo_inmueble` (
  `IDTipoDocumento` varchar(36) NOT NULL,
  `IDTipoInmueble` char(36) NOT NULL,
  `ObligatorioNichosNegocio` tinyint(4) DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `EsPublico` tinyint(4) DEFAULT 1,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTipoDocumento`,`IDTipoInmueble`),
  KEY `fk_obligatiorios_IDTipoDocumento_idx` (`IDTipoDocumento`),
  KEY `FK_Obligatorios_TipoInmueble_idx` (`IDTipoInmueble`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_proveedores`;
CREATE TABLE `gd_proveedores` (
  `IDProveedor` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveProveedor` varchar(20) NOT NULL,
  `NombreProveedor` varchar(45) NOT NULL,
  `DescripcionProveedor` text DEFAULT NULL,
  `RazonSocialProveedor` varchar(128) NOT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDProveedor`),
  KEY `FK_Proveedor_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_servicios_publicos`;
CREATE TABLE `gd_servicios_publicos` (
  `IDServicioPublico` char(36) NOT NULL DEFAULT (uuid()),
  `IDTipoServicioPublico` char(36) DEFAULT NULL,
  `PredioID` char(36) DEFAULT NULL,
  `IDEdificio` char(36) DEFAULT NULL,
  `IDNivel` char(36) DEFAULT NULL,
  `IDZona` char(36) DEFAULT NULL,
  `CuentaServicioPublico` varchar(45) DEFAULT NULL,
  `IDProveedor` char(36) DEFAULT NULL,
  `IDTipoDocumento` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDServicioPublico`),
  KEY `ServicioTipoServicioPublico_idx` (`IDTipoServicioPublico`),
  KEY `ServicioTipoDocumento_idx` (`IDTipoDocumento`),
  KEY `FK_ServicioPub_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_ServicioPub_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_ServicioPub_TipoDocumento` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`),
  CONSTRAINT `FK_ServicioPub_TipoServicioPublico` FOREIGN KEY (`IDTipoServicioPublico`) REFERENCES `gd_tipos_serviciopublico` (`IDTipoServicioPublico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_tipos_conceptoservicio`;
CREATE TABLE `gd_tipos_conceptoservicio` (
  `IDTipoConceptoServicio` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveTipoConceptoServicio` varchar(20) NOT NULL,
  `NombreTipoConceptoServicio` varchar(45) NOT NULL,
  `DescripcionTipoConceptoServicio` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoConceptoServicio`),
  KEY `FK_TiposConceptoServicio_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_tipos_documento`;
CREATE TABLE `gd_tipos_documento` (
  `IDTipoDocumento` varchar(36) NOT NULL DEFAULT (uuid()),
  `IDCategoriaDocumento` varchar(36) NOT NULL,
  `ClaveTipoDocumento` varchar(20) NOT NULL,
  `NombreTipoDocumento` varchar(45) NOT NULL,
  `DescripcionTipoDocumento` text DEFAULT NULL,
  `EsMultiple` tinyint(4) DEFAULT 1,
  `IDPais` char(36) DEFAULT NULL,
  `IDEstado` char(36) DEFAULT NULL,
  `IDMunicipio` char(36) DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoDocumento`),
  KEY `FK_TiposDocumento_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_tipos_serviciopublico`;
CREATE TABLE `gd_tipos_serviciopublico` (
  `IDTipoServicioPublico` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveTipoServicioPublico` varchar(20) NOT NULL,
  `NombreTipoServicioPublico` varchar(45) NOT NULL,
  `DescripcionTipoServicioPublico` text DEFAULT NULL,
  `IDTipoDocumento` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`IDTipoServicioPublico`),
  KEY `TipoDocumento_idx` (`IDTipoDocumento`),
  KEY `FK_TipoServicioPub_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_TipoServicioPub_TipoDocumento` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_ver_categorias`;
CREATE TABLE `gd_ver_categorias` (
  `IDCategoriaDoc` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDCategoriaDoc`,`IDCliente`),
  KEY `FK_VerCategoria_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_VerCategoria_CatDoc` FOREIGN KEY (`IDCategoriaDoc`) REFERENCES `gd_categorias_doc` (`IDCategoriaDoc`),
  CONSTRAINT `FK_VerCategoria_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_ver_grupos`;
CREATE TABLE `gd_ver_grupos` (
  `IDGrupoDoc` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDGrupoDoc`,`IDCliente`),
  KEY `VerGrupo_Clientes_idx` (`IDCliente`),
  CONSTRAINT `VerGrupo_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `VerGrupo_GrupoDoc` FOREIGN KEY (`IDGrupoDoc`) REFERENCES `gd_grupos_doc` (`IDGrupoDoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_ver_obligatorios_publicos`;
CREATE TABLE `gd_ver_obligatorios_publicos` (
  `IDTipoDocumento` char(36) NOT NULL,
  `IDTipoInmueble` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTipoDocumento`,`IDTipoInmueble`,`IDCliente`),
  KEY `FK_Obligatorios_Clientes_idx` (`IDCliente`),
  KEY `FK_Obligatorios_TipoInmueble_idx` (`IDTipoInmueble`),
  CONSTRAINT `FK_VerObligatorios_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_VerObligatorios_TipoDoc` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_obligatorios_tipo_inmueble` (`IDTipoDocumento`),
  CONSTRAINT `FK_VerObligatorios_TipoInmueble` FOREIGN KEY (`IDTipoInmueble`) REFERENCES `gd_obligatorios_tipo_inmueble` (`IDTipoInmueble`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `gd_ver_tipos_publicos`;
CREATE TABLE `gd_ver_tipos_publicos` (
  `IDTipoDocumento` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTipoDocumento`,`IDCliente`),
  KEY `FK_Vertipos_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_VerTipos_TipoDoc` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`),
  CONSTRAINT `FK_Vertipos_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_caracteristicas`;
CREATE TABLE `in_caracteristicas` (
  `IDCaracteristica` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'dfd',
  `ClaveCaracteristica` varchar(20) DEFAULT NULL,
  `NombreCaracteristica` varchar(45) DEFAULT NULL,
  `DescripcionCaracteristica` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDCaracteristica`),
  KEY `FK_Caracteristicas_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_carac_tipo_equipo`;
CREATE TABLE `in_carac_tipo_equipo` (
  `IDTipoEquipo` char(36) NOT NULL,
  `IDCaracteristica` char(36) NOT NULL,
  `IDUnidadMedida` char(36) DEFAULT NULL,
  `ValorCaracTipoEquipo` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoEquipo`,`IDCaracteristica`),
  KEY `CaracUnidadMedida_idx` (`IDUnidadMedida`),
  KEY `CaracCaracteristicas_idx` (`IDCaracteristica`),
  KEY `FK_Carac_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Carac_TipoEquipo` FOREIGN KEY (`IDTipoEquipo`) REFERENCES `in_tipos_equipo` (`IDTipoEquipo`),
  CONSTRAINT `FK_Carac_UnidadMedida` FOREIGN KEY (`IDUnidadMedida`) REFERENCES `in_unidades_medida` (`IDUnidadMedida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_carac_unidades`;
CREATE TABLE `in_carac_unidades` (
  `IDCaracteristica` char(36) NOT NULL,
  `IDUnidadMedida` char(36) NOT NULL,
  `FechaCreacionObjeto` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0,
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDCaracteristica`,`IDUnidadMedida`),
  KEY `fk_in_caracteristicas_has_in_unidades_medida_in_unidades_me_idx` (`IDUnidadMedida`),
  KEY `fk_in_caracteristicas_has_in_unidades_medida_in_caracterist_idx` (`IDCaracteristica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_contratos_de_servicio`;
CREATE TABLE `in_contratos_de_servicio` (
  `IDContratoServicio` char(36) NOT NULL DEFAULT (uuid()),
  `IDProveedor` char(36) DEFAULT NULL,
  `FechaFirmaContratoServicio` date DEFAULT NULL,
  `IniVIgenciaContratoServicio` date DEFAULT NULL,
  `FinVigenciaContratoServicio` date DEFAULT NULL,
  `Vigente` tinyint(4) DEFAULT 0,
  `IDImagenContrato` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDContratoServicio`),
  KEY `IDImagenContrato_idx` (`IDImagenContrato`),
  KEY `ContratoProveedor_idx` (`IDProveedor`),
  KEY `FK_ContratoServ_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_ContratoServ_Imagen` FOREIGN KEY (`IDImagenContrato`) REFERENCES `arch_archivos` (`IDArchivo`),
  CONSTRAINT `FK_ContratoServ_Proveedor` FOREIGN KEY (`IDProveedor`) REFERENCES `gd_proveedores` (`IDProveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_contratos_equipos`;
CREATE TABLE `in_contratos_equipos` (
  `IDEquipo` char(36) NOT NULL,
  `IDContratoServicio` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEquipo`,`IDContratoServicio`),
  KEY `RelContratos_idx` (`IDContratoServicio`),
  KEY `FK_EquiposContratos_Clientes_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_equipos`;
CREATE TABLE `in_equipos` (
  `IDEquipo` char(36) NOT NULL DEFAULT (uuid()),
  `IDSubsistema` char(36) NOT NULL,
  `IDTipoEquipo` char(36) NOT NULL,
  `IDZona` char(36) NOT NULL,
  `ClaveEquipo` varchar(20) NOT NULL,
  `NoSerieEquipo` varchar(45) NOT NULL,
  `DescripcionEquipo` text DEFAULT NULL,
  `FechaCompraEquipo` date DEFAULT NULL,
  `FechaUltMantEquipo` date DEFAULT NULL,
  `FechaProxMantEquipo` date DEFAULT NULL,
  `IDTipoDocumento` char(36) NOT NULL,
  `IDCategoriaDocumento` char(36) NOT NULL,
  `ClaveTipoDocumento` varchar(20) NOT NULL,
  `NombreTipoDocumento` varchar(45) NOT NULL,
  `DescripcionTipoDocumento` text DEFAULT NULL,
  `IDImagenEquipo` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEquipo`),
  KEY `EquipoTipoEquipo_idx` (`IDTipoEquipo`),
  KEY `EquipoZona_idx` (`IDZona`),
  KEY `EquipoSubsistema_idx` (`IDSubsistema`),
  KEY `EquipoImagen_idx` (`IDImagenEquipo`),
  KEY `FK_Equipo_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Equipo_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_Equipo_Imagen` FOREIGN KEY (`IDImagenEquipo`) REFERENCES `arch_archivos` (`IDArchivo`),
  CONSTRAINT `FK_Equipo_Instancia` FOREIGN KEY (`IDEquipo`) REFERENCES `track_instancias` (`IDInstancia`),
  CONSTRAINT `FK_Equipo_Subsistema` FOREIGN KEY (`IDSubsistema`) REFERENCES `in_subsistemas` (`IDSubsistema`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_fabricantes`;
CREATE TABLE `in_fabricantes` (
  `IDFabricante` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveFabricante` varchar(20) NOT NULL,
  `NombreFabricante` varchar(45) NOT NULL,
  `DescripcionFabricante` text DEFAULT NULL,
  `KeyWordsFabricante` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDFabricante`),
  KEY `FK_Fabricante_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_keywords`;
CREATE TABLE `in_keywords` (
  `IDKeyword` varchar(20) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDKeyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_keyword_fabricante`;
CREATE TABLE `in_keyword_fabricante` (
  `IDKeyword` varchar(20) NOT NULL,
  `IDFabricante` char(36) NOT NULL DEFAULT (uuid()),
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDKeyword`,`IDFabricante`),
  KEY `KeyFabricante_idx` (`IDFabricante`),
  CONSTRAINT `FK_KeyKeylist` FOREIGN KEY (`IDKeyword`) REFERENCES `in_keywords` (`IDKeyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_sistemas`;
CREATE TABLE `in_sistemas` (
  `IDSistema` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveSistema` varchar(20) NOT NULL,
  `NombreSistema` varchar(45) NOT NULL,
  `DescripcionSistema` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDSistema`),
  KEY `FK_Sistemas_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_subsistemas`;
CREATE TABLE `in_subsistemas` (
  `IDSubsistema` char(36) NOT NULL DEFAULT (uuid()),
  `IDSistema` char(36) NOT NULL,
  `ClaveTipoDocumento` varchar(20) NOT NULL,
  `NombreSubsistema` varchar(45) NOT NULL,
  `DescripcionSubsistema` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDSubsistema`),
  KEY `SusistemaSistema_idx` (`IDSistema`),
  KEY `FK_Subsistema_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_Subsistema_Sistema` FOREIGN KEY (`IDSistema`) REFERENCES `in_sistemas` (`IDSistema`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_tipos_equipo`;
CREATE TABLE `in_tipos_equipo` (
  `IDTipoEquipo` char(36) NOT NULL DEFAULT (uuid()),
  `IDFabricante` char(36) DEFAULT NULL,
  `ClaveTipoEquipo` varchar(20) NOT NULL,
  `NombreTipoEquipo` varchar(45) NOT NULL,
  `DescripcionTipoEquipo` text DEFAULT NULL,
  `MarcaTipoEquipo` varchar(45) NOT NULL,
  `ModeloTipoEquipo` varchar(45) DEFAULT NULL,
  `VidaUtilTipoEquipo` int(11) DEFAULT NULL,
  `RecomendacionesTipoEquipo` text DEFAULT NULL,
  `URLTipoEquipo` varchar(128) DEFAULT NULL,
  `ImagenTipoEquipo` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoEquipo`),
  KEY `TipoEquipoFabricante_idx` (`IDFabricante`),
  KEY `ImagenTIpoEquipo_idx` (`ImagenTipoEquipo`),
  KEY `FK_TipoEquipo_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_TipoEquipo_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `in_unidades_medida`;
CREATE TABLE `in_unidades_medida` (
  `IDUnidadMedida` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveUnidadMedida` char(20) NOT NULL,
  `NombreUnidadMedida` char(45) NOT NULL,
  `DescripcionUnidadMedida` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDUnidadMedida`),
  KEY `FK_Unidad_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_catalogos`;
CREATE TABLE `ms_catalogos` (
  `IDCatalogo` char(36) NOT NULL,
  `IDModulo` char(36) DEFAULT NULL,
  `NombreCatalogo` varchar(45) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  `EsPublico` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDCatalogo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_empresas`;
CREATE TABLE `ms_empresas` (
  `IDEmpresa` char(36) NOT NULL,
  `ClaveEmpresa` varchar(20) NOT NULL,
  `NombreEmpresa` varchar(45) NOT NULL,
  `DescripcionEmpresa` text DEFAULT NULL,
  `RazonSocialEmpresa` varchar(128) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEmpresa`),
  KEY `FK_Empresa_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_funciones`;
CREATE TABLE `ms_funciones` (
  `IDModulo` char(36) DEFAULT NULL,
  `IDOpcion` char(36) DEFAULT NULL,
  `IDFuncion` char(36) NOT NULL,
  `ClaveFuncion` varchar(20) DEFAULT NULL,
  `NombreFuncion` varchar(45) DEFAULT NULL,
  `DescripcionFuncion` text DEFAULT NULL,
  `PermisoFuncion` int(11) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDFuncion`),
  UNIQUE KEY `IDFuncion_UNIQUE` (`IDFuncion`),
  KEY `FuncionPermisos_idx` (`PermisoFuncion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_modulos`;
CREATE TABLE `ms_modulos` (
  `IDModulo` char(36) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `ClaveModulo` varchar(20) DEFAULT NULL,
  `NombreModulo` varchar(45) DEFAULT NULL,
  `DescripcionModulo` text DEFAULT NULL,
  `IDPermiso` int(11) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `href` longtext DEFAULT NULL,
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDModulo`),
  UNIQUE KEY `IDModulo_UNIQUE` (`IDModulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_opciones`;
CREATE TABLE `ms_opciones` (
  `IDModulo` char(36) NOT NULL,
  `IDOpcion` char(36) NOT NULL,
  `ClaveModulo` varchar(20) DEFAULT NULL,
  `NombreModulo` varchar(45) DEFAULT NULL,
  `DescripcionModulo` text DEFAULT NULL,
  `PermisoOpcion` int(11) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `href` longtext DEFAULT NULL,
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDModulo`,`IDOpcion`),
  KEY `OpcionPermisos_idx` (`PermisoOpcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_permisos`;
CREATE TABLE `ms_permisos` (
  `IDPermiso` int(11) NOT NULL,
  `NombrePermiso` varchar(45) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`IDPermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_permisos_rol`;
CREATE TABLE `ms_permisos_rol` (
  `IDPermisoRol` int(11) NOT NULL,
  `IDRol` int(11) NOT NULL,
  `IDPermiso` int(11) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`IDPermisoRol`),
  KEY `PermisoRolRoles_idx` (`IDRol`),
  KEY `PermisoRolPermisos_idx` (`IDPermiso`),
  CONSTRAINT `FK_PermisoRol_Roles` FOREIGN KEY (`IDRol`) REFERENCES `ms_roles` (`IDRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_personas`;
CREATE TABLE `ms_personas` (
  `IDPersona` char(36) NOT NULL DEFAULT (uuid()),
  `Usuario` varchar(45) DEFAULT NULL,
  `PasswordPersona` longtext DEFAULT NULL,
  `NombrePersona` varchar(45) DEFAULT NULL,
  `ApellidoPaternoPersona` varchar(45) DEFAULT NULL,
  `ApellidoMaternoPersona` varchar(45) DEFAULT NULL,
  `EmailPersona` varchar(45) NOT NULL,
  `EmailVerificadoPersona` tinyint(4) DEFAULT 0,
  `TelefonoPersona` varchar(20) DEFAULT NULL,
  `CelularPersona` varchar(20) DEFAULT NULL,
  `CelularVerificadoPersona` tinyint(4) DEFAULT 0,
  `ActivoPersona` tinyint(4) DEFAULT 1,
  `CambiarPasswordPersona` tinyint(4) DEFAULT 0,
  `UltimoAccesoPersona` datetime DEFAULT NULL,
  `FallidosDiaPersona` int(11) DEFAULT NULL,
  `FallidosAcumPersona` int(11) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `IDRol` bigint(20) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `logo` longtext DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  PRIMARY KEY (`IDPersona`),
  KEY `FK_Persona_Cliente_idx` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_personas_devices`;
CREATE TABLE `ms_personas_devices` (
  `IDPersonaDevice` char(36) NOT NULL,
  `IDPersona` varchar(45) DEFAULT NULL,
  `deviceID` longtext DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDPersonaDevice`),
  UNIQUE KEY `IDPersonaDevice_UNIQUE` (`IDPersonaDevice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_roles`;
CREATE TABLE `ms_roles` (
  `IDRol` int(11) NOT NULL,
  `ClaveRol` varchar(20) DEFAULT NULL,
  `NombreRol` varchar(45) DEFAULT NULL,
  `DescripcionRol` text DEFAULT NULL,
  `RolCliente` tinyint(4) DEFAULT NULL,
  `RolEmpresa` tinyint(4) DEFAULT NULL,
  `RolProveedor` tinyint(4) DEFAULT NULL,
  `RolPredio` tinyint(4) DEFAULT NULL,
  `RolZona` tinyint(4) DEFAULT NULL,
  `AtiendeOTRol` tinyint(4) DEFAULT NULL,
  `AtiendeTKRol` tinyint(4) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_roles_con_clientes`;
CREATE TABLE `ms_roles_con_clientes` (
  `IDRolesCliente` char(36) NOT NULL DEFAULT (uuid()),
  `IDPersona` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRolesCliente`),
  KEY `RolClientePersonas_idx` (`IDPersona`),
  KEY `RolClienteRoles_idx` (`IDRol`),
  KEY `FK_RolCliente_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_RolCliente_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_roles_en_empresas`;
CREATE TABLE `ms_roles_en_empresas` (
  `IDRolEmpres` char(36) NOT NULL DEFAULT (uuid()),
  `IDPersona` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `IDEmpresa` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRolEmpres`),
  KEY `RolesEmpresaPersonas_idx` (`IDPersona`),
  KEY `RolesEmpresaEmpresas_idx` (`IDEmpresa`),
  KEY `RolEmpresaRoles_idx` (`IDRol`),
  KEY `FK_RolEmpresa_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_RolEmpresa_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `ms_roles_en_predios`;
CREATE TABLE `ms_roles_en_predios` (
  `IDRolesPredio` char(36) NOT NULL DEFAULT (uuid()),
  `IDPersona` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `IDPredio` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRolesPredio`),
  KEY `RolPredioPersonas_idx` (`IDPersona`),
  KEY `RolPredioPredios_idx` (`IDPredio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `ms_roles_en_zonas`;
CREATE TABLE `ms_roles_en_zonas` (
  `IDRolZona` char(36) NOT NULL DEFAULT (uuid()),
  `IDPersona` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `IDZona` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRolZona`),
  KEY `RolZonaPersonas_idx` (`IDPersona`),
  KEY `RolZonaZonas_idx` (`IDZona`),
  KEY `RolZonaRoles_idx` (`IDRol`),
  KEY `FK_RolZona_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_RolZona_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_RolZona_Zonas` FOREIGN KEY (`IDZona`) REFERENCES `conf_zonas` (`IDZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_roles_proveedor`;
CREATE TABLE `ms_roles_proveedor` (
  `IDRolProveedor` char(36) NOT NULL DEFAULT (uuid()),
  `IDPersona` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `IDProveedor` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRolProveedor`),
  KEY `RolEmpresaPersonas_idx` (`IDPersona`),
  KEY `RolProveedorRoles_idx` (`IDRol`),
  KEY `RolProveedorProveedores_idx` (`IDProveedor`),
  KEY `FK_RolProveedor_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_RolProveedor_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ms_roles_rutinas`;
CREATE TABLE `ms_roles_rutinas` (
  `IDRolRutina` char(36) NOT NULL,
  `IDRutina` char(36) DEFAULT NULL,
  `IDRol` int(11) DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDRolRutina`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `IDNotificacion` char(36) NOT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `titulo` text DEFAULT NULL,
  `IDInstancia` char(36) DEFAULT NULL,
  `IDModulo` char(36) DEFAULT NULL,
  `IDPredio` char(36) DEFAULT NULL,
  `NombreModulo` varchar(255) DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDNotificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_comentarios_orden_trabajo`;
CREATE TABLE `ot_comentarios_orden_trabajo` (
  `IDComentarioOT` char(36) NOT NULL,
  `IDOT` char(36) NOT NULL,
  `IDPersona` char(36) NOT NULL,
  `FechaComentarioOT` datetime DEFAULT NULL,
  `ComentarioOT` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDComentarioOT`),
  KEY `FK_ComentarioOT_OT_idx` (`IDOT`),
  KEY `FK_ComentarioOT_Clientes_idx` (`IDCliente`),
  KEY `FK_ComentarioOT_Personas_idx` (`IDPersona`),
  CONSTRAINT `FK_ComentarioOT_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_ComentarioOT_OT` FOREIGN KEY (`IDOT`) REFERENCES `ot_orden_trabajo` (`IDOT`),
  CONSTRAINT `FK_ComentarioOT_Personas` FOREIGN KEY (`IDPersona`) REFERENCES `ms_personas` (`IDPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_costos_orden_trabajo`;
CREATE TABLE `ot_costos_orden_trabajo` (
  `IDCostoOT` char(36) NOT NULL DEFAULT (uuid()),
  `IDOT` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una evidencia anexa a un ticket.',
  `CantidadCostoOT` float NOT NULL,
  `ClaveCostoOT` varchar(20) NOT NULL,
  `DescripcionCostoOT` text NOT NULL,
  `UnidadCostoOT` varchar(45) NOT NULL,
  `PrecioCostoOT` float NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `TasaIVACostoOT` float NOT NULL,
  `IVACostoOT` float NOT NULL COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDCostoOT`),
  KEY `FK_CostoOT_OT_idx` (`IDOT`),
  KEY `FK_CostoOT_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_CostoOT_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_CostoOT_OT` FOREIGN KEY (`IDOT`) REFERENCES `ot_orden_trabajo` (`IDOT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_evidencias_orden_trabajo`;
CREATE TABLE `ot_evidencias_orden_trabajo` (
  `IDOT` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una evidencia anexa a un ticket.',
  `IDArchivo` char(36) NOT NULL,
  `IDPersona` char(36) NOT NULL,
  `TituloEvidenciaTK` varchar(45) DEFAULT NULL,
  `ComentarioEvidenciaTK` text DEFAULT NULL,
  `FechaEvidenciaTK` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDOT`,`IDArchivo`),
  KEY `FK_Evidencias__idx` (`IDOT`),
  KEY `FK_EvidenciaTK_Archivos_idx` (`IDArchivo`),
  KEY `FK_EvidenciaTK_Personas_idx` (`IDPersona`),
  KEY `FK_EvidenciaTK_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_EvidenciaTK_Archivos` FOREIGN KEY (`IDArchivo`) REFERENCES `arch_archivos` (`IDArchivo`),
  CONSTRAINT `FK_EvidenciaTK_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_EvidenciaTK_OT` FOREIGN KEY (`IDOT`) REFERENCES `ot_orden_trabajo` (`IDOT`),
  CONSTRAINT `FK_EvidenciaTK_Personas` FOREIGN KEY (`IDPersona`) REFERENCES `ms_personas` (`IDPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_evidencias_tareas`;
CREATE TABLE `ot_evidencias_tareas` (
  `IDTareaOT` char(36) DEFAULT NULL,
  `IDArchivo` char(36) DEFAULT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `ComentarioEvidencia` text DEFAULT NULL,
  `FechaEvidencia` datetime DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `Borrado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `ot_mediciones`;
CREATE TABLE `ot_mediciones` (
  `IDOtMediciones` char(36) NOT NULL DEFAULT (uuid()),
  `IDOT` char(36) NOT NULL,
  `ClaveLectura` varchar(20) DEFAULT NULL,
  `VariableLectura` varchar(45) DEFAULT NULL,
  `UnidadLectura` varchar(20) DEFAULT NULL,
  `DescripcionLectura` text DEFAULT NULL,
  `ManualLectura` tinyint(4) DEFAULT 0,
  `AlertaMinimoLectura` tinyint(4) DEFAULT 0,
  `ValorMinimoLectura` float DEFAULT NULL,
  `AlertaMaximoLectura` tinyint(4) DEFAULT 0,
  `ValorMaximoLectura` float DEFAULT NULL,
  `IDCliente` char(36) NOT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDOtMediciones`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `ot_orden_trabajo`;
CREATE TABLE `ot_orden_trabajo` (
  `IDOT` char(36) NOT NULL,
  `idDefinicionRutina` char(1) DEFAULT NULL,
  `NumOT` varchar(20) DEFAULT NULL,
  `IDTipoOT` char(36) DEFAULT NULL,
  `IDPropiedad` char(36) DEFAULT NULL,
  `PersonalExternoOT` tinyint(4) DEFAULT 0,
  `IDProveedor` char(36) DEFAULT NULL,
  `IDContrato` char(36) DEFAULT NULL,
  `HayOrdenCompraOT` tinyint(4) DEFAULT 0,
  `IDOrdenCompra` char(36) DEFAULT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `IDPredio` char(36) DEFAULT NULL,
  `IDEdificio` char(36) DEFAULT NULL,
  `IDNivel` char(36) DEFAULT NULL,
  `FechaIniOrdenTrabajo` varchar(45) DEFAULT NULL,
  `FechaFinOT` varchar(45) DEFAULT NULL,
  `FechaInicioReal` datetime DEFAULT NULL,
  `HorasRealesOT` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `IDEquipo` char(36) DEFAULT NULL,
  `IDDocumento` char(36) DEFAULT NULL,
  `IDZona` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `duracion_estimada` varchar(45) DEFAULT NULL,
  `DescripcionOt` text DEFAULT NULL,
  PRIMARY KEY (`IDOT`),
  KEY `FK_OrdenTrabajo_TipoOT_idx` (`IDTipoOT`),
  KEY `FK_OrdenTrabajo_Clientes_idx` (`IDCliente`),
  KEY `FK_OrdenTrabajo_Personas_idx` (`IDPersona`),
  CONSTRAINT `FK_OrdenTrabajo_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_OrdenTrabajo_Instancias` FOREIGN KEY (`IDOT`) REFERENCES `track_instancias` (`IDInstancia`),
  CONSTRAINT `FK_OrdenTrabajo_Personas` FOREIGN KEY (`IDPersona`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_OrdenTrabajo_TipoOT` FOREIGN KEY (`IDTipoOT`) REFERENCES `ot_tipo_orden_trabajo` (`IDTipoOT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_requisitos`;
CREATE TABLE `ot_requisitos` (
  `IDOtRequisitos` char(36) NOT NULL,
  `IDOT` char(36) NOT NULL,
  `OrdenRequisitoAccion` int(11) DEFAULT NULL,
  `DescripcionRequisitoAccion` text DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDOtRequisitos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `ot_rondin`;
CREATE TABLE `ot_rondin` (
  `IDOTRondin` char(36) NOT NULL,
  `IDZona` char(36) DEFAULT NULL,
  `IDOT` char(36) DEFAULT NULL,
  `Orden` int(11) DEFAULT NULL,
  `estatus_check` tinyint(4) DEFAULT 0,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDOTRondin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `ot_tareas_orden`;
CREATE TABLE `ot_tareas_orden` (
  `IDTareaOT` char(36) NOT NULL,
  `IDOT` char(36) NOT NULL,
  `OrdenTareaOT` int(11) DEFAULT NULL,
  `DescripcionTareaOT` text NOT NULL,
  `RealizadoTareaOT` tinyint(4) NOT NULL DEFAULT 0,
  `FechaRealizadaTareaOT` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTareaOT`),
  KEY `FK_TareaOT_Clientes_idx` (`IDCliente`),
  KEY `FK_TareaOT_OT` (`IDOT`),
  CONSTRAINT `FK_TareaOT_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_TareaOT_OT` FOREIGN KEY (`IDOT`) REFERENCES `ot_orden_trabajo` (`IDOT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_tipo_orden_trabajo`;
CREATE TABLE `ot_tipo_orden_trabajo` (
  `IDTipoOT` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveTipoOT` varchar(20) NOT NULL,
  `NombreTipoOT` varchar(45) NOT NULL,
  `DescripcionTipoOT` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTipoOT`),
  KEY `FK_TipoOT_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_TipoOT_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `ot_valores_orden`;
CREATE TABLE `ot_valores_orden` (
  `IDValorOT` char(36) NOT NULL,
  `IDOT` char(36) NOT NULL,
  `VariableValorOT` varchar(45) DEFAULT NULL,
  `ValorOT` float DEFAULT NULL,
  `FechaValorOrden` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDValorOT`),
  KEY `FK_ValoresOrden_OT` (`IDOT`),
  CONSTRAINT `FK_ValoresOrden_OT` FOREIGN KEY (`IDOT`) REFERENCES `ot_orden_trabajo` (`IDOT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `Usuario` varchar(255) DEFAULT NULL,
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IDModel` char(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Permiso` tinyint(4) DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `modulo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_acciones_mantenimiento`;
CREATE TABLE `plan_acciones_mantenimiento` (
  `IDAccionesMantenimiento` char(36) NOT NULL DEFAULT (uuid()),
  `IDPlan` char(36) NOT NULL,
  `IDDefinicionAccion` char(36) NOT NULL,
  `IDEquipo` char(36) DEFAULT NULL,
  `IDOrdenTrabajo` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `duracion_estimada` varchar(45) DEFAULT NULL,
  `costo_estimado` varchar(45) DEFAULT NULL,
  `FechaInicioAccion` date DEFAULT NULL,
  `FechaFinAccion` date DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDAccionesMantenimiento`),
  KEY `AccionesDefMant_idx` (`IDDefinicionAccion`),
  KEY `AccionesPlanMant_idx` (`IDPlan`),
  KEY `AccionesEquipo_idx` (`IDEquipo`),
  KEY `FK_AccionMant_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_AccionesM_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_AccionesM_PlanMant` FOREIGN KEY (`IDPlan`) REFERENCES `plan_planes` (`IDPlan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_acciones_renovacion`;
CREATE TABLE `plan_acciones_renovacion` (
  `IDAccionesRenovacion` char(36) NOT NULL DEFAULT (uuid()),
  `IDPlan` char(36) NOT NULL,
  `IDDefinicionAccion` char(36) NOT NULL,
  `IDDocumento` char(36) DEFAULT NULL,
  `IDOrdenTrabajo` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `duracion_estimada` varchar(45) DEFAULT NULL,
  `costo_estimado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDAccionesRenovacion`),
  KEY `PlanDefRenovacion_idx` (`IDDefinicionAccion`),
  KEY `AccionesPlanRenovacion_idx` (`IDPlan`),
  KEY `AccionesDocumento_idx` (`IDDocumento`),
  CONSTRAINT `FK_AccionesR_PlanRenovacion` FOREIGN KEY (`IDPlan`) REFERENCES `plan_planes` (`IDPlan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_acciones`;
CREATE TABLE `plan_def_acciones` (
  `IDDefinicionAccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDTipoAccion` char(36) NOT NULL,
  `ClaveDefinicionAccion` char(20) DEFAULT NULL,
  `NombreDefinicionAccion` char(45) DEFAULT NULL,
  `DescripcionDefinicionAccion` text DEFAULT NULL,
  `DuracionDefinicionAccion` int(11) DEFAULT NULL,
  `EsPublicaDefinicionAccion` tinyint(4) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDefinicionAccion`),
  KEY `AccionTipoAccion_idx` (`IDTipoAccion`),
  KEY `FK_DefAccion_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefAccion_TipoAccion` FOREIGN KEY (`IDTipoAccion`) REFERENCES `plan_tipos_accion` (`IDTipoAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_accion_comentario`;
CREATE TABLE `plan_def_accion_comentario` (
  `IDComentarioAccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `OrdenComentarioAccion` int(11) DEFAULT NULL,
  `DescripcionComentarioAccion` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  KEY `ComentarioDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_Comentario_Cliente_idx` (`IDCliente`),
  CONSTRAINT `FK_DefComentario_Cliente` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_DefComentario_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_accion_requisitos`;
CREATE TABLE `plan_def_accion_requisitos` (
  `IDRequisitoAccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `OrdenRequisitoAccion` int(11) DEFAULT NULL,
  `DescripcionRequisitoAccion` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDRequisitoAccion`),
  KEY `RequisitoDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefRequisitos_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefRequisitos_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_accion_roles`;
CREATE TABLE `plan_def_accion_roles` (
  `IDRolAcccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `IDRol` char(36) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDRolAcccion`),
  KEY `RolDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefRol_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefRol_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_accion_tareas`;
CREATE TABLE `plan_def_accion_tareas` (
  `IDTareaAccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `OrdenTareaAccion` int(11) DEFAULT NULL,
  `DescripcionTareaAccion` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTareaAccion`),
  KEY `ActividadDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefActividad_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefActividad_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_DefActividad_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_accion_valores`;
CREATE TABLE `plan_def_accion_valores` (
  `IDValorAccion` varchar(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` varchar(36) NOT NULL,
  `VariableValorAccion` varchar(45) DEFAULT NULL,
  `DescripcionValorAccion` text DEFAULT NULL,
  `IDUnidad` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDValorAccion`),
  KEY `ValorDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefValor_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefValor_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_DefValor_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_comentarios`;
CREATE TABLE `plan_def_comentarios` (
  `IDComentarioAcccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `FechaComentarioAccion` varchar(45) DEFAULT NULL,
  `ComentarioAccion` text DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDComentarioAcccion`),
  KEY `RolDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefRol_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefComenta_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_DefComta_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_lecturas`;
CREATE TABLE `plan_def_lecturas` (
  `IDDefLecturas` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `ClaveLectura` varchar(20) DEFAULT NULL,
  `VariableLectura` varchar(45) DEFAULT NULL,
  `UnidadLectura` varchar(20) DEFAULT NULL,
  `DescripcionLectura` text DEFAULT NULL,
  `ManualLectura` tinyint(4) DEFAULT 0,
  `AlertarMinimoLectura` tinyint(4) DEFAULT 0,
  `ValorMinimoLectura` float DEFAULT NULL,
  `AlertarMaximoLectura` tinyint(4) DEFAULT 0,
  `ValorMaximoLectura` float DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDefLecturas`),
  KEY `MantDefinicion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefMant_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefLectura_DefAccion` FOREIGN KEY (`IDDefinicionAccion`) REFERENCES `plan_def_acciones` (`IDDefinicionAccion`),
  CONSTRAINT `FK_DefLectura_DefClientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_mantenimiento`;
CREATE TABLE `plan_def_mantenimiento` (
  `IDDefMant` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) NOT NULL,
  `IDTipoEquipo` char(36) DEFAULT NULL,
  `RequiereLecturaDefMant` tinyint(4) DEFAULT 0,
  `LecturaManualDefMant` tinyint(4) DEFAULT 0,
  `AlertarMinimoDefMant` tinyint(4) DEFAULT 0,
  `ValorMinimoDefMant` float DEFAULT NULL,
  `AlertarMaximoDefMant` tinyint(4) DEFAULT 0,
  `PeriodicidadDefManto` int(11) DEFAULT NULL,
  `ValorMaximoDefMant` float DEFAULT NULL,
  `RequiereParoDefMant` tinyint(4) DEFAULT 0,
  `HorasParoDefMant` float DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDefMant`),
  KEY `MantDefinicion_idx` (`IDDefinicionAccion`),
  KEY `DefMantTiposEquipo_idx` (`IDTipoEquipo`),
  KEY `FK_DefMant_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefMant_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_def_renovacion`;
CREATE TABLE `plan_def_renovacion` (
  `IDDefRenovacion` char(36) NOT NULL DEFAULT (uuid()),
  `IDDefinicionAccion` char(36) DEFAULT NULL,
  `IDTipoDocumento` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDDefRenovacion`),
  KEY `DefRenovacionTipoDoc_idx` (`IDTipoDocumento`),
  KEY `DefRenovacionDefAccion_idx` (`IDDefinicionAccion`),
  KEY `FK_DefRenovación_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_DefRenovacion_TipoDoc` FOREIGN KEY (`IDTipoDocumento`) REFERENCES `gd_tipos_documento` (`IDTipoDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_planes`;
CREATE TABLE `plan_planes` (
  `IDPlan` char(36) NOT NULL DEFAULT (uuid()),
  `IDTipoPlan` char(36) NOT NULL,
  `ClavePlan` varchar(20) NOT NULL,
  `NombrePlan` varchar(45) NOT NULL,
  `DescripcionPlan` text DEFAULT NULL,
  `FechaInicioPlan` date DEFAULT NULL,
  `FechaFinPlan` date DEFAULT NULL,
  `IDPredioPlan` char(36) NOT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`IDPlan`),
  KEY `PlanTiposPlan_idx` (`IDTipoPlan`),
  KEY `FK_Plan_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_Plan_Instancia` FOREIGN KEY (`IDPlan`) REFERENCES `track_instancias` (`IDInstancia`),
  CONSTRAINT `FK_Plan_TiposPlan` FOREIGN KEY (`IDTipoPlan`) REFERENCES `plan_tipos_plan` (`IDTipoPlan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_tipos_accion`;
CREATE TABLE `plan_tipos_accion` (
  `IDTipoAccion` char(36) NOT NULL DEFAULT (uuid()),
  `IDCliente` char(36) DEFAULT NULL,
  `ClaveTipoAccion` varchar(20) DEFAULT NULL,
  `NombreTipoAccion` varchar(45) DEFAULT NULL,
  `DescripcionTipoAccion` text DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `plan_tipos_plan`;
CREATE TABLE `plan_tipos_plan` (
  `IDTipoPlan` char(36) NOT NULL DEFAULT (uuid()),
  `ClaveTipoPlan` varchar(20) NOT NULL,
  `NombreTipoPlan` varchar(45) NOT NULL,
  `DescripcionTipoPlan` text DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoPlan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `pushnotifications`;
CREATE TABLE `pushnotifications` (
  `IDPushNotification` char(36) NOT NULL,
  `IDDevice` text DEFAULT NULL,
  `IDPersona` varchar(45) DEFAULT NULL,
  `Estado` tinyint(4) DEFAULT 1,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDPushNotification`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `AtiendeOTRol` tinyint(4) DEFAULT NULL,
  `AtiendeTKRol` tinyint(4) DEFAULT NULL,
  `IDTipoRol` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `rut_def_rutinas`;
CREATE TABLE `rut_def_rutinas` (
  `IDDefinicionRutina` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica la definición de una rutina',
  `IDTipoRutina` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla rut_tipos_rutina correspondiente al tipo de rutina del que se trate. (limpieza, rondín, equipo o general)',
  `IDDefinicionAccion` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla plan_def_acciones correspondiente a la definición de la acción que se realizará en la rutina.',
  `IDPredio` char(36) NOT NULL,
  `ClaveDefRutina` varchar(45) NOT NULL COMMENT 'Un texto de hasta 20 caracteres que identifica a la rutina',
  `NombreDefRutina` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se conoce comunmente a la rutina',
  `DescripcioDefRutina` varchar(45) DEFAULT NULL COMMENT 'Un texto que describa la rutina',
  `FechaInicioDefRutina` date DEFAULT NULL COMMENT 'Primera fecha en la que se podrá realizar la rutina.',
  `flagFinalizaDefRutina` tinyint(4) DEFAULT NULL COMMENT 'Bandera que indica si hay una fecha en la que la rutina dejará de realizarse. (1= FechaFinDefRutina es obligatoria, 0= FechaFinDefRutina es opcional)',
  `FechaFinDefRutina` date DEFAULT NULL COMMENT 'Ultima fecha en la que se deberá realizar la rutina, solo es obligatoria cuando la bandera flagFinalizaDefRutina es 1.',
  `flagEsDiariaDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar diariamente. Se modifica de acuerdo con los valores de FrecuenciaDiasDefRutina y flagDiasLaborablesDefRutina. Es excluyente con los valores de flagEsSemanalDefRutina, flagEsMensualDefRutina y flagEsAnualDefRutina.',
  `flagEsSemanalDefRutina` tinyint(4) DEFAULT 0 COMMENT 'Indica que la rutina se puede realizar semanalmente. Se modifica de acuerdo con los valores de flagDomingoDefRutina, flagLunesDefRutina, flagMartesDefRutina, flagMiercolesDefRutina, flagJuevesDefRutina, flagViernesDefRutina, flagSabadoDefRutina. Es excluyente con los valores de flagEsDiarialDefRutina y flagEsMensualDefRutina.',
  `flagEsMensualDefRutina` tinyint(4) DEFAULT 0 COMMENT 'Indica que la rutina se puede realizar semanalmente. Se modifica de acuerdo con los valores de flagDomingoDefRutina, flagLunesDefRutina, flagMartesDefRutina, flagMiercolesDefRutina, flagJuevesDefRutina, flagViernesDefRutina, flagSabadoDefRutina. Es excluyente con los valores de flagEsDiarialDefRutina y flagEsSemanallDefRutina.',
  `FrecuenciaDefRutina` int(11) DEFAULT 1 COMMENT 'Indica el periodo en el que se debe repetir la rutina. En días cuando flagEsDiariaDefRutina=1, en semanas cuandoflagEsSemanalDefRutina=1, en meses cuando es flagEsMensualDefRutina=1',
  `flagDiasLaborDefRutina` tinyint(4) DEFAULT 0 COMMENT 'Indica si para las repeticiones se incluyen solo los días laborables. Solo incluir dias laborables cuando flagDiasLaborDefRutina = 1. Considerar todos los dias laborables cuando flagDiasLaborDefRutina = 0.',
  `flagDomingoDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en domingo. Sí cuando flagDomingoDefRutina = 1, No cuando flagDomingoDefRutina = 0.',
  `flagLunesDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en lunes. Sí cuando flagLunesDefRutina = 1, No cuando flagLunesDefRutina = 0.',
  `flagMartesDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en martes. Sí cuando flagMartesDefRutina = 1, No cuando flagMartesDefRutina = 0.',
  `flagMiércolesDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en miércoles. Sí cuando flagMiercolesDefRutina = 1, No cuando flagMiercolesDefRutina = 0.',
  `flagJuevesDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en jueves. Sí cuando flagJuevesDefRutina = 1, No cuando flagJuevesDefRutina = 0.',
  `flagViernesDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en viernes. Sí cuando flagViernesDefRutina = 1, No cuando flagViernesDefRutina = 0.',
  `flagSabadoDefRutina` tinyint(4) DEFAULT 1 COMMENT 'Indica que la rutina se puede realizar en viernes. Sí cuando flagSabadoDefRutina = 1, No cuando flagSábadoDefRutina = 0.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el registro por última vez.',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDefinicionRutina`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `rut_rutinas_equipos`;
CREATE TABLE `rut_rutinas_equipos` (
  `IDDefinicionRutina` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla plan_def_rutina correspondiente a la rutina con la que se están asociando los equipos.',
  `IDEquipo` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla in_equipos correspondiente al equipo con el que se asocia la rutina.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el registro por última vez.\n',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDDefinicionRutina`,`IDEquipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `rut_rutinas_zonas`;
CREATE TABLE `rut_rutinas_zonas` (
  `IDDefinicionRutina` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla plan_def_rutina correspondiente a la rutina con la que se están asociando las zonas.',
  `IDZona` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_zonas correspondiente a las zonas asociadas a la definición de la rutina.',
  `OrdenRutinasZonas` int(11) DEFAULT NULL COMMENT 'Secuencia en la que se ordenan las zonas ligadas a una rutina. Cuando la rutina es de tipo rondín, determina el órden en que se debe visitar cada punto del recorrido.',
  `FechaActualizacionObjeto` datetime DEFAULT NULL,
  `FechaCreacionObjeto` datetime DEFAULT NULL,
  `borrado` tinyint(4) NOT NULL,
  PRIMARY KEY (`IDDefinicionRutina`,`IDZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `rut_tipos_rutina`;
CREATE TABLE `rut_tipos_rutina` (
  `IDTipoRutina` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un tipo de rutina',
  `ClaveTipoRutina` varchar(20) NOT NULL COMMENT 'Texto alfanumérico de hasta 20 posiciones en el que se puede definir la clave que identifique un tipo de rutina',
  `NombreTipoRutina` varchar(45) NOT NULL COMMENT 'Texto alfanumérico de hasta 45 posiciones en el que se puede nombrar a cada tipo de rutina para identificarla',
  `DescripcionTipo` text DEFAULT NULL COMMENT 'Texto alfanumérico en el que se puede describir ampliamente el objetivo y alcances de cada tipo de rutina.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoRutina`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tipos_roles`;
CREATE TABLE `tipos_roles` (
  `IDTipoRol` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) DEFAULT NULL,
  `Descripcion` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IDTipoRol`),
  UNIQUE KEY `IDTipoRol_UNIQUE` (`IDTipoRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_archivos_adjuntos`;
CREATE TABLE `tk_archivos_adjuntos` (
  `IDArchivoAdjunto` char(36) NOT NULL,
  `IDTicket` char(36) DEFAULT NULL,
  `IDArchivo` char(36) DEFAULT NULL,
  `IDPersona` char(36) DEFAULT NULL,
  `ComentarioEvidencia` text DEFAULT NULL,
  `FechaEvidencia` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) DEFAULT NULL,
  `FechaCreacionObjeto` timestamp NULL DEFAULT NULL,
  `FechaActualizacionObjeto` timestamp NULL DEFAULT NULL,
  `Borrado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`IDArchivoAdjunto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_categorias_ticket`;
CREATE TABLE `tk_categorias_ticket` (
  `IDCatTicket` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una categoría que representa el nivel más alto en que se puede clasificar una solicitud de servicio o ticket.',
  `ClaveCatTicket` varchar(45) NOT NULL COMMENT 'Un texto de hasta 20 caracteres que identifica una categoría que representa el nivel más alto en que se puede clasificar una solicitud de servicio o ticket.',
  `NombreCatTicket` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se conoce comunmente un grupo mayor de servicios o categoría que representa el nivel más alto en que se puede clasificar una solicitud de servicio o ticket.',
  `DescripcionCatTicket` text DEFAULT NULL COMMENT 'Un texto que describa la categoría. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el objeto por última vez',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDCatTicket`),
  KEY `FK_CatTicket_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_CatTicket_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_comentarios`;
CREATE TABLE `tk_comentarios` (
  `IDComentarioTicket` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica el comentario a un ticket.',
  `IDTicket` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla tk_tickets, correspondiente al ticket al que se le está agregando un comentario',
  `IDPersona` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona que escribió el comentario',
  `ComentarioTicket` text DEFAULT NULL COMMENT 'Comentario al ticket.',
  `FechaComentarioTicket` datetime DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDComentarioTicket`),
  KEY `FK_ComentarioTicket_Clientes_idx` (`IDCliente`),
  KEY `FK_ComentarioTicket_Tickets_idx` (`IDTicket`),
  KEY `FK_ComentarioTicket_Personas_idx` (`IDPersona`),
  CONSTRAINT `FK_ComentarioTicket_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_ComentarioTicket_Personas` FOREIGN KEY (`IDPersona`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_ComentarioTicket_Tickets` FOREIGN KEY (`IDTicket`) REFERENCES `tk_ticket` (`IDTicket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_evidencias`;
CREATE TABLE `tk_evidencias` (
  `IDTicket` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una evidencia anexa a un ticket.',
  `IDArchivo` char(36) NOT NULL,
  `IDPersona` char(36) NOT NULL,
  `ComentarioEvidencia` text DEFAULT NULL,
  `FechaEvidencia` varchar(45) DEFAULT NULL,
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDTicket`,`IDArchivo`),
  KEY `FK_Evidencia_Archivos_idx` (`IDArchivo`),
  KEY `FK_Evidencia_Personas_idx` (`IDPersona`),
  KEY `FK_Evidencias_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_Evidencia_Archivos` FOREIGN KEY (`IDArchivo`) REFERENCES `arch_archivos` (`IDArchivo`),
  CONSTRAINT `FK_Evidencia_Personas` FOREIGN KEY (`IDPersona`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_Evidencia_Tickets` FOREIGN KEY (`IDTicket`) REFERENCES `tk_ticket` (`IDTicket`),
  CONSTRAINT `FK_Evidencias_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_nivel_servicio`;
CREATE TABLE `tk_nivel_servicio` (
  `IDNivelServicio` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica el nivel de servicio que debe tener un ticket de acuerdo con su tipo.',
  `ClaveNivelServicio` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres que identifica el nivel de servicio que puede tener un ticket de acuerdo con su tipo',
  `NombreNivelServicio` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se conoce comunmente el nivel de servicio que puede tener un ticket de acuerdo con su tipo.',
  `DescripcionNivelServicio` text DEFAULT NULL COMMENT 'Un texto que describa el nivel de servicio que tiene el ticket de acuerdo con su tipo. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `TRespuestaNivelServicio` float NOT NULL COMMENT 'Tiempo en que se debe atender un ticket después de que el usuario lo abre para cumplir con el nivel de servicio.',
  `TSolucionNivelServicio` float NOT NULL COMMENT 'Tiempo en el que se debe solucionar un tipo de ticket desde el momento en que se abre el ticket.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el objeto por última vez',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDNivelServicio`),
  KEY `FK_TipoTicket_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_TipoTicket_Clientes0` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_subcategorias_ticket`;
CREATE TABLE `tk_subcategorias_ticket` (
  `IDSubCatTicket` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una subcategoría que representa el segundo nivel en que se puede clasificar una solicitud de servicio o ticket.',
  `IDCatTicket` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla tk_categorias_ticket correspondiente a la categoría a la que pertenece la subcategoría que se describe en este registro.',
  `ClaveSubCatTicket` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres que identifica una subcategoría que representa el segundo nivel en que se puede clasificar una solicitud de servicio o ticket.',
  `NombreSubCatTicket` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se conoce comunmente un subconjunto de servicios dentro de una categoría que representa el segundo nivel en que se puede clasificar una solicitud de servicio o ticket.',
  `DescripcionSubCatTicket` text DEFAULT NULL COMMENT 'Un texto que describa el nivel de servicio que tiene el ticket de acuerdo con su tipo. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el objeto por última vez',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDSubCatTicket`),
  KEY `FK_SubCatTicket_CatTicket_idx` (`IDCatTicket`),
  KEY `FK_SubCatTicket_Clientes_idx` (`IDCliente`),
  CONSTRAINT `FK_SubCatTicket_CatTicket` FOREIGN KEY (`IDCatTicket`) REFERENCES `tk_categorias_ticket` (`IDCatTicket`),
  CONSTRAINT `FK_SubCatTicket_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_ticket`;
CREATE TABLE `tk_ticket` (
  `IDTicket` char(36) NOT NULL COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica una solicitud de servicio o ticket.',
  `NombreTicket` varchar(255) DEFAULT NULL,
  `DescripcionTicket` longtext DEFAULT NULL,
  `IDTipoTicket` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla tk_tipos_ticket, correspondiente al tipo de servicio del que se trata.',
  `IDSolicitaTicket` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona que solicita el servicio. ',
  `IDAbreTicket` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona que abrió el ticket. Este campo es igual al campo IDSolicitaTicket excepto en los casos en los que el ticket se abre por encargo de otra persona.',
  `IDAtiendeTicket` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona a la que se le asigna la solución del ticket.',
  `IDSupervisaTicket` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona de help desk que estará dandole seguimiento a la atención del ticket.\n',
  `IDCierraTicket` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas, correspondiente a la persona que debe cerrar o finalmente cerró el ticket.',
  `IDEquipo` char(36) DEFAULT NULL,
  `IDPredio` char(36) DEFAULT NULL,
  `IDEdificio` char(36) DEFAULT NULL,
  `IDNivel` char(36) DEFAULT NULL,
  `IDZona` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_zonas, correspondiente a la zona en la que se requiere el servicio.',
  `IDNivelServicio` char(36) DEFAULT NULL,
  `TAcumSolucionTicket` float NOT NULL DEFAULT 0 COMMENT 'Acumulador del tiempo de solución del ticket. Este campo sólo contabiliza el tiempo efectivo por lo que no acumula tiempos cuando el ticket está suspendido en espera de información del cliente o resuelto y esperando que el cliente lo cierre.\n',
  `HoraIniciaSolucionTicket` datetime DEFAULT NULL COMMENT 'La fecha y hora en la que el ticket es asignado a la persona que debe solucionarlo.',
  `HoraTerminaSolucionTicket` datetime DEFAULT NULL COMMENT 'La fecha y la hora en la que concluyó la atención del ticket.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `NoTk` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDTicket`),
  KEY `FK_Ticket_Zona_idx` (`IDZona`),
  KEY `FK_Ticket_TipoTicket_idx` (`IDTipoTicket`),
  KEY `FK_SolicitaTicket_Personas_idx` (`IDSolicitaTicket`),
  KEY `FK_AbreTicket_Personas_idx` (`IDAbreTicket`),
  KEY `FK_CierraTicket_Personas_idx` (`IDCierraTicket`),
  KEY `FK_Ticket_Clientes_idx` (`IDCliente`),
  KEY `FK_SupervisaTicket_Personas_idx` (`IDSupervisaTicket`),
  KEY `FK_AtiendeTicket_Personas_idx` (`IDAtiendeTicket`),
  CONSTRAINT `FK_AbreTicket_Personas` FOREIGN KEY (`IDAbreTicket`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_AtiendeTicket_Personas` FOREIGN KEY (`IDAtiendeTicket`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_CierraTicket_Personas` FOREIGN KEY (`IDCierraTicket`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_SolicitaTicket_Personas` FOREIGN KEY (`IDSolicitaTicket`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_SupervisaTicket_Personas` FOREIGN KEY (`IDSupervisaTicket`) REFERENCES `ms_personas` (`IDPersona`),
  CONSTRAINT `FK_Ticket_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_Ticket_Instancias` FOREIGN KEY (`IDTicket`) REFERENCES `track_instancias` (`IDInstancia`),
  CONSTRAINT `FK_Ticket_TipoTicket` FOREIGN KEY (`IDTipoTicket`) REFERENCES `tk_tipos_ticket` (`IDTipoTicket`),
  CONSTRAINT `FK_Ticket_Zona` FOREIGN KEY (`IDZona`) REFERENCES `conf_zonas` (`IDZona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `tk_tipos_ticket`;
CREATE TABLE `tk_tipos_ticket` (
  `IDTipoTicket` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica el tipo de servicio que es el nivel más bajo en que se puede clasificar una solicitud de servicio o ticket.',
  `IDSubCatTicket` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla tk_subcategorias_ticket correspondiente a la subcategoría a la que pertenece el tipo de servicio que se describe en este registro.',
  `ClaveTipoTicket` varchar(20) NOT NULL COMMENT 'Un texto de hasta 20 caracteres que identifica un tipo de servicio que representa el nivel más bajo en que se puede clasificar una solicitud de servicio o ticket.',
  `NombreTipoTicket` varchar(45) NOT NULL COMMENT 'Hasta 45 caracteres con el nombre con el que se conoce comunmente tipo de servicio dentro de una subcategoría, y que representa el nivel mas bajo en que se puede clasificar una solicitud de servicio o ticket.',
  `DescripcionTipoTicket` text DEFAULT NULL COMMENT 'Un texto que describa las características de un tipo de servicio. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDNivelServicio` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla tk_nivel_servicio correspondiente al nivel de servicio que tiene el tipo de ticket.',
  `AsignaAutomaticoTicket` tinyint(4) DEFAULT 0 COMMENT 'Valor booleano que indica si este tipo de tickets se deben enviar a Help Desk para que se asigne un responsable de solucionarlo de manera manual (AsignaAutomaticoTicket=FALSE) o se debe asociar automáticamente un responsable  (AsignaAutomáticoTicket=TRUE) de acuerdo con los valores configurados en los campos RolAsignaAutoTicket y RolAsignaDefaultTicket.',
  `RolAsignaAutoTicket` char(36) DEFAULT NULL COMMENT 'Rol en el predio al que debe asignarse este tipo de ticket cuando el campo AsignaAutomaticoTicket=TRUE',
  `RolAsignaDefaultTicket` char(36) DEFAULT NULL COMMENT 'Rol en el predio al que debe asignarse este tipo de ticket cuando no exista un usuario con el rol definido en el campo RolAsignaAutoTicket y el campo AsignaAutomaticoTicket=TRUE',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la que se modificó el objeto por última vez',
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  `EsPublico` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Valor booleano que define si el registro es visible por todas las personas (EsPublico=True=1) o sólamente cuando la persona es parte de un cliente (EsPublico=False=0)',
  PRIMARY KEY (`IDTipoTicket`),
  KEY `FK_TipoTicket_SubCategoria_idx` (`IDSubCatTicket`),
  KEY `FK_TipoTicket_Clientes_idx` (`IDCliente`),
  KEY `FK_TipoTicket_NivelesServicio_idx` (`IDNivelServicio`),
  CONSTRAINT `FK_TipoTicket_Clientes` FOREIGN KEY (`IDCliente`) REFERENCES `conf_clientes` (`IDCliente`),
  CONSTRAINT `FK_TipoTicket_NivelesServicio` FOREIGN KEY (`IDNivelServicio`) REFERENCES `tk_nivel_servicio` (`IDNivelServicio`),
  CONSTRAINT `FK_TipoTicket_SubCategoria` FOREIGN KEY (`IDSubCatTicket`) REFERENCES `tk_subcategorias_ticket` (`IDSubCatTicket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_estados`;
CREATE TABLE `track_estados` (
  `IDEstado` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un estado de un flujo de trabajo.\n',
  `IDFlujo` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_flujos correspondiente al flujo al que pertenece el estado que representa este registro.',
  `ClaveEstado` varchar(20) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con un nombre corto que se utilizará para identificar un estado.',
  `NombreEstado` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que se utilizará para definir este estado. Este nombre será el que se muestre en las aplicaciones para indicar el estado de los objetos.',
  `DescripcionEstado` text DEFAULT NULL COMMENT 'Un texto que describa el estado y sus características. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEstado`),
  KEY `EstadoFlujo_idx` (`IDFlujo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_eventos`;
CREATE TABLE `track_eventos` (
  `IDEvento` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica  una acción o condición externa que puede provocar una transición de un estado a otro.',
  `ClaveEvento` varchar(255) NOT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que se utilizará para identificar el evento.',
  `NombreEvento` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que se utilizará para definir este evento. Este nombre será el que se muestre en las aplicaciones para indicar los eventos de un objeto.',
  `DescripcionEvento` text DEFAULT NULL COMMENT 'Un texto que describa el evento. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_flujos`;
CREATE TABLE `track_flujos` (
  `IDFlujo` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un flujo de trabajo.\n',
  `ClaveFlujo` char(36) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con un nombre corto que se utilizará para identificar un estado.',
  `NombreFlujo` varchar(36) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que se utilizará para identificar este flujo. Este nombre será el que se muestre en las aplicaciones para indicar flujo al que pertenecen los objetos.',
  `DescripcionFlujo` text DEFAULT NULL COMMENT 'Un texto que describa el flujo, sus objetivos, los objetos que lo utilizan, etc. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDFlujo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_historia_instancias`;
CREATE TABLE `track_historia_instancias` (
  `IDHistoriaInstancia` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica el registro de la ocurrencia de un evento dentro de un flujo de trabajo.',
  `IDInstancia` char(36) NOT NULL COMMENT 'UUID con la llave del objeto de negocio que fue transitado de un estado a otro del flujo tras la ocurrencia de un evento.',
  `IDEstadoAnteriorHistoria` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado en el que estaba el objeto antes de que ocurriera el evento.',
  `IDEstadoSiguienteHistoria` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado al que llegó el objeto despues de que ocurriera el evento.',
  `IDEventoHistoria` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_eventos del evento que se registró.',
  `FechaEventoAnteriorHistoria` datetime DEFAULT NULL COMMENT 'Fecha y hora de la última transición del objeto antes de que ocurriera el evento.',
  `FechaEventoHistoria` datetime DEFAULT NULL COMMENT 'Fecha y hora en la que ocurrió el evento.',
  `IDPersonaTransitoHistoria` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas de la persona que generó el evento.',
  `IDCliente` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDHistoriaInstancia`),
  KEY `HistoriaInstancia_idx` (`IDInstancia`),
  KEY `HistoriaEstadoAnt_idx` (`IDEstadoAnteriorHistoria`),
  KEY `HistoriaEstadoSig_idx` (`IDEstadoSiguienteHistoria`),
  CONSTRAINT `FK_Historia_Instancia` FOREIGN KEY (`IDInstancia`) REFERENCES `track_instancias` (`IDInstancia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_instancias`;
CREATE TABLE `track_instancias` (
  `IDInstancia` char(36) NOT NULL DEFAULT (uuid()) COMMENT 'Identificador Universal Único version 1 (UUIDv1)  que identifica un objeto en particular que participa en un flujo de trabajo. Esta UUID es la misma que objeto de negocio tiene en su propia tabla. ',
  `IDFlujo` char(45) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla track_flujos correspondiente al flujo en el que participa el objeto de negocio.',
  `IDEstadoActualInstancia` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado en el que está el objeto actualmente.',
  `IDEstadoAnteriorInstancia` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado en el que estaba el objeto antes de llegar al estado actual.',
  `IDUltimoEventoInstancia` char(36) DEFAULT NULL,
  `FechaUltimoEventoInstancia` datetime DEFAULT NULL COMMENT 'Fecha del día y hora en la que ocurrió la ultima transición del objeto.',
  `IDPersonaTransitoInstancia` char(36) DEFAULT NULL COMMENT 'UUID con la llave del registro en la tabla ms_personas de la persona que generó el último evento.',
  `IDCliente` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla conf_clientes correspondiente al cliente al que está asociado el registro',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDInstancia`),
  KEY `InstanciaFlujo_idx` (`IDFlujo`),
  KEY `InstanciaEstadoAnterior_idx` (`IDEstadoAnteriorInstancia`),
  KEY `InstanciaEstadoActual_idx` (`IDEstadoActualInstancia`),
  KEY `FK_Instancia_Personas_idx` (`IDPersonaTransitoInstancia`),
  CONSTRAINT `FK_Instancia_Flujo` FOREIGN KEY (`IDFlujo`) REFERENCES `track_flujos` (`IDFlujo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `track_transitos`;
CREATE TABLE `track_transitos` (
  `ClaveTransito` varchar(255) DEFAULT NULL COMMENT 'Un texto de hasta 20 caracteres con la clave que se utilizará para identificar la transicion.',
  `NombreTransito` varchar(45) DEFAULT NULL COMMENT 'Hasta 45 caracteres con el nombre que se utilizará para definir esta transición de estados. Este nombre será el que se muestre en las aplicaciones para indicar las transiciones de un objeto.',
  `DescripcionTransito` text DEFAULT NULL COMMENT 'Un texto que describa el evento, la transición y los efectos sobre el objeto. Este texto se utilizará en las aplicaciones como ayuda para el usuario.',
  `IDFlujo` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_flujos correspondiente al flujo al que pertenece la transición que representa este registro.',
  `IDEstadoActualTransito` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado en el que se origina la transición que genera el evento que representa este registro.',
  `IDEvento` char(36) NOT NULL,
  `IDEstadoSiguienteTransito` char(36) NOT NULL COMMENT 'UUID con la llave del registro en la tabla track_estados correspondiente al estado al que se llega despues de la transición que genere el evento que representa este registro.',
  `PermisoTransito` int(11) DEFAULT NULL,
  `AccionTransito` varchar(512) DEFAULT NULL COMMENT 'Un texto de hasta 128 caracteres, en el que se indica el o los procesos que se realizan al ocurrir el evento.',
  `FechaCreacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en la que se creó el registro en la base de datos.',
  `FechaActualizacionObjeto` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Borrado` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Valor booleano que indica si el registro fue borrado y no debe considerarse en las consultas. Los registros borrados se conservan para preservar la integridad referencial de la base de datos.',
  PRIMARY KEY (`IDFlujo`,`IDEstadoActualTransito`,`IDEvento`),
  KEY `TransitoFlujo_idx` (`IDFlujo`),
  KEY `TransitoEstadoAct_idx` (`IDEstadoActualTransito`),
  KEY `TransitoEstadoSig_idx` (`IDEstadoSiguienteTransito`),
  KEY `TransitoEvento` (`IDFlujo`,`IDEstadoActualTransito`,`IDEvento`),
  KEY `FK_Transito_Evento_idx` (`IDEvento`),
  CONSTRAINT `FK_Transito_Flujo` FOREIGN KEY (`IDFlujo`) REFERENCES `track_flujos` (`IDFlujo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- 2025-07-17 17:10:12
