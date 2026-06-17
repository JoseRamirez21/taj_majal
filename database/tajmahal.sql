-- ============================================================
--  TAJ MAHAL KARAOKE BAR - Base de Datos Completa
--  Sistema de Gestión Profesional
-- ============================================================

CREATE DATABASE IF NOT EXISTS tajmahal_karaoke CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tajmahal_karaoke;

-- -------------------------------------------------------
-- USUARIOS Y ROLES
-- -------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    telefono VARCHAR(20),
    rol ENUM('admin','operador','cajero','mesero') DEFAULT 'mesero',
    avatar VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- SALAS / AMBIENTES
-- -------------------------------------------------------
CREATE TABLE salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    capacidad INT DEFAULT 10,
    tipo ENUM('privada','publica','vip') DEFAULT 'publica',
    precio_hora DECIMAL(10,2) DEFAULT 0.00,
    imagen VARCHAR(255),
    equipos JSON COMMENT 'Lista de equipos: micrófonos, pantallas, luces, etc.',
    activa TINYINT(1) DEFAULT 1,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- MESAS
-- -------------------------------------------------------
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT,
    numero INT NOT NULL,
    capacidad INT DEFAULT 4,
    estado ENUM('libre','ocupada','reservada','mantenimiento') DEFAULT 'libre',
    qr_code VARCHAR(255),
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- CANCIONES
-- -------------------------------------------------------
CREATE TABLE canciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    artista VARCHAR(150) NOT NULL,
    album VARCHAR(150),
    anio YEAR,
    genero VARCHAR(80),
    idioma VARCHAR(50) DEFAULT 'Español',
    duracion_seg INT DEFAULT 0,
    codigo VARCHAR(20) UNIQUE,
    youtube_id VARCHAR(20) COMMENT 'ID del video de YouTube para embed',
    letra TEXT,
    portada VARCHAR(255),
    popularidad INT DEFAULT 0,
    activa TINYINT(1) DEFAULT 1,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    FULLTEXT INDEX idx_busqueda (titulo, artista, album)
);

-- -------------------------------------------------------
-- COLA DE KARAOKE (QUEUE)
-- -------------------------------------------------------
CREATE TABLE cola_karaoke (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT,
    mesa_id INT,
    cancion_id INT NOT NULL,
    cantante_nombre VARCHAR(100) DEFAULT 'Anónimo',
    estado ENUM('en_espera','cantando','completada','saltada') DEFAULT 'en_espera',
    posicion INT DEFAULT 0,
    solicitado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    iniciado_en DATETIME,
    finalizado_en DATETIME,
    nota_publica TEXT,
    FOREIGN KEY (cancion_id) REFERENCES canciones(id),
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE SET NULL,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- RESERVACIONES
-- -------------------------------------------------------
CREATE TABLE reservaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT,
    mesa_id INT,
    cliente_nombre VARCHAR(150) NOT NULL,
    cliente_telefono VARCHAR(20),
    cliente_email VARCHAR(150),
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    n_personas INT DEFAULT 1,
    estado ENUM('pendiente','confirmada','cancelada','completada') DEFAULT 'pendiente',
    observaciones TEXT,
    monto_anticipado DECIMAL(10,2) DEFAULT 0.00,
    creado_por INT,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE SET NULL,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- CATEGORÍAS DE PRODUCTOS
-- -------------------------------------------------------
CREATE TABLE categorias_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    icono VARCHAR(50),
    color VARCHAR(20) DEFAULT '#ffd700',
    orden INT DEFAULT 0,
    activa TINYINT(1) DEFAULT 1
);

-- -------------------------------------------------------
-- PRODUCTOS (BEBIDAS / COMIDA)
-- -------------------------------------------------------
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    precio_costo DECIMAL(10,2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    destacado TINYINT(1) DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_productos(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- PEDIDOS
-- -------------------------------------------------------
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT,
    sala_id INT,
    cliente_nombre VARCHAR(150),
    mesero_id INT,
    estado ENUM('pendiente','en_preparacion','listo','entregado','cancelado') DEFAULT 'pendiente',
    total DECIMAL(10,2) DEFAULT 0.00,
    observaciones TEXT,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    entregado_en DATETIME,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
    FOREIGN KEY (mesero_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- DETALLE DE PEDIDOS
-- -------------------------------------------------------
CREATE TABLE pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    nota VARCHAR(255),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- -------------------------------------------------------
-- BOLETAS / CUENTAS
-- -------------------------------------------------------
CREATE TABLE boletas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_boleta VARCHAR(20) UNIQUE,
    mesa_id INT,
    sala_id INT,
    cajero_id INT,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    igv DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) DEFAULT 0.00,
    metodo_pago ENUM('efectivo','tarjeta','yape','plin','transferencia') DEFAULT 'efectivo',
    estado ENUM('abierta','pagada','anulada') DEFAULT 'abierta',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    pagado_en DATETIME,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
    FOREIGN KEY (cajero_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- -------------------------------------------------------
-- PUNTUACIONES / VOTACIONES DE KARAOKE
-- -------------------------------------------------------
CREATE TABLE puntuaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cola_id INT NOT NULL,
    mesa_id INT,
    puntos INT DEFAULT 5 CHECK (puntos BETWEEN 1 AND 10),
    aplauso TINYINT(1) DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cola_id) REFERENCES cola_karaoke(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- NOTIFICACIONES
-- -------------------------------------------------------
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('pedido','reserva','cola','sistema','alerta') DEFAULT 'sistema',
    titulo VARCHAR(150) NOT NULL,
    mensaje TEXT,
    para_rol VARCHAR(50),
    leida TINYINT(1) DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- CONFIGURACIÓN DEL SISTEMA
-- -------------------------------------------------------
CREATE TABLE configuracion (
    clave VARCHAR(100) PRIMARY KEY,
    valor TEXT,
    descripcion VARCHAR(255)
);

-- ====================================================
--  DATOS INICIALES
-- ====================================================

-- Admin
INSERT INTO usuarios (nombre, usuario, password, email, rol) VALUES
('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@tajmahal.pe', 'admin'),
('Operador 1', 'operador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'op@tajmahal.pe', 'operador'),
('Cajero Principal', 'cajero', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'caja@tajmahal.pe', 'cajero');
-- password para todos: password

-- Salas
INSERT INTO salas (nombre, descripcion, capacidad, tipo, precio_hora) VALUES
('Sala Imperial', 'La sala principal con pantalla gigante 4K y sistema de sonido surround 7.1', 50, 'publica', 0.00),
('VIP Bollywood', 'Sala privada decorada con temática india, capacidad para grupos exclusivos', 15, 'vip', 150.00),
('Suite Maharaja', 'La suite más lujosa con karaoke privado, bar incluido y decoración real', 8, 'privada', 250.00),
('Sala Ganges', 'Ambiente relajado con vistas al jardín y sistema de karaoke HD', 20, 'publica', 0.00);

-- Mesas
INSERT INTO mesas (sala_id, numero, capacidad, estado) VALUES
(1,1,6,'libre'),(1,2,4,'libre'),(1,3,8,'libre'),(1,4,4,'libre'),(1,5,6,'libre'),
(2,6,6,'libre'),(2,7,8,'libre'),(2,8,4,'libre'),
(3,9,8,'libre'),(3,10,6,'libre'),
(4,11,4,'libre'),(4,12,6,'libre'),(4,13,4,'libre');

-- Categorías de productos
INSERT INTO categorias_productos (nombre, icono, color, orden) VALUES
('Cócteles', '🍹', '#e74c3c', 1),
('Cervezas', '🍺', '#f39c12', 2),
('Vinos', '🍷', '#8e44ad', 3),
('Tragos', '🥃', '#d35400', 4),
('Sin Alcohol', '🥤', '#27ae60', 5),
('Piqueos', '🍟', '#e67e22', 6),
('Platos', '🍽️', '#16a085', 7);

-- Productos
INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock) VALUES
(1,'Pisco Sour Clásico','El clásico peruano con pisco quebranta',22.00,100),
(1,'Mojito','Ron, menta fresca, limón y soda',18.00,100),
(1,'Margarita','Tequila, triple sec y limón con sal',20.00,100),
(1,'Cosmopolitan','Vodka, cranberry, limón y cointreau',22.00,100),
(2,'Cristal','Cerveza peruana clásica 620ml',10.00,200),
(2,'Pilsen Callao','Cerveza rubia premium 620ml',10.00,200),
(2,'Corona','Cerveza importada con limón 355ml',14.00,150),
(3,'Vino Tinto Tabernero','Copa de vino tinto premium',18.00,80),
(3,'Vino Blanco','Copa de vino blanco seco',18.00,80),
(4,'Ron con Cola','Ron Cartavio con Coca-Cola',15.00,100),
(4,'Whisky on the rocks','Whisky importado con hielo',35.00,60),
(5,'Agua Mineral','500ml',5.00,200),
(5,'Coca-Cola','Lata 355ml',7.00,150),
(5,'Jugo de Maracuyá','Fresco natural',10.00,80),
(6,'Tequeños','12 unidades con guacamole',25.00,50),
(6,'Alitas BBQ','12 alitas con salsa BBQ',35.00,40),
(6,'Nachos con Queso','Nachos crujientes con dip de queso',22.00,60),
(7,'Lomo Saltado','Clásico peruano con arroz y papas',38.00,30),
(7,'Ceviche','Ceviche mixto de la casa',45.00,25),
(7,'Pizza Hawaiana','Pizza mediana con piña y jamón',35.00,20);

-- Canciones populares
INSERT INTO canciones (titulo, artista, genero, idioma, duracion_seg, codigo, youtube_id, popularidad) VALUES
('Bésame Mucho','Consuelo Velázquez','Bolero','Español',195,'KAR001','ZAMf8yIjUHM',95),
('La Bamba','Ritchie Valens','Folk Rock','Español',130,'KAR002','SXKlJeHOF3M',90),
('Despacito','Luis Fonsi ft. Daddy Yankee','Reggaeton','Español',229,'KAR003','kTJczUoc26U',98),
('Mi Gente','J Balvin & Willy William','Reggaeton','Español',178,'KAR004','wnJ6LuUFpMo',88),
('Perfecta','Ed Sheeran','Pop','Inglés',263,'KAR005','2Vv-BfVoq4g',92),
('Bohemian Rhapsody','Queen','Rock','Inglés',354,'KAR006','fJ9rUzIMcZQ',97),
('My Way','Frank Sinatra','Pop Clásico','Inglés',270,'KAR007','6E2hYDIFDIU',85),
('Hotel California','Eagles','Rock','Inglés',391,'KAR008','EqPtz5qN7HM',89),
('Amores Como el Nuestro','Jerry Rivera','Salsa','Español',240,'KAR009','oW5ufqDMPmM',82),
('Vivir Mi Vida','Marc Anthony','Salsa','Español',248,'KAR010','YXnjy5KN2pk',94),
('Shape of You','Ed Sheeran','Pop','Inglés',234,'KAR011','JGwWNGJdvx8',91),
('Tusa','Karol G & Nicki Minaj','Reggaeton','Español',200,'KAR012','G9MBmXPHX-8',86),
('Contigo Aprendí','Luis Miguel','Bolero','Español',257,'KAR013','DQZpvFGJFsM',88),
('La Incondicional','Luis Miguel','Pop','Español',240,'KAR014','oSPxVYNQ2Q0',87),
('Rosalía - Malamente','Rosalía','Flamenco Moderno','Español',183,'KAR015','6S3ISlvlEbs',80),
('Thriller','Michael Jackson','Pop/R&B','Inglés',357,'KAR016','sOnqjkJTMaA',93),
('Livin on a Prayer','Bon Jovi','Rock','Inglés',252,'KAR017','lDK9QqIzhwk',90),
('Total Eclipse of the Heart','Bonnie Tyler','Pop Rock','Inglés',326,'KAR018','lcOxiU1JX3w',84),
('Don Omar - Danza Kuduro','Don Omar','Reggaeton','Español',195,'KAR019','7zp1TbLFPp8',89),
('Señorita','Shawn Mendes & Camila Cabello','Pop','Inglés',190,'KAR020','Pkh8UtuejGw',87);

-- Configuración
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_bar','Taj Mahal Karaoke Bar','Nombre del establecimiento'),
('direccion','Av. La Cultura 123, Ayacucho, Perú','Dirección'),
('telefono','+51 999 888 777','Teléfono de contacto'),
('email','info@tajmahal.pe','Email de contacto'),
('igv_porcentaje','18','Porcentaje de IGV'),
('moneda','PEN','Moneda'),
('simbolo_moneda','S/.','Símbolo de moneda'),
('hora_apertura','20:00','Hora de apertura'),
('hora_cierre','04:00','Hora de cierre'),
('max_canciones_cola','50','Máximo de canciones en cola'),
('tiempo_rotacion_min','10','Tiempo máximo de espera entre turnos (minutos)'),
('logo','','Logo del bar'),
('color_primario','#ffd700','Color principal del tema'),
('facebook','https://facebook.com/tajmahalkaraoke','Facebook'),
('instagram','https://instagram.com/tajmahalkaraoke','Instagram');
