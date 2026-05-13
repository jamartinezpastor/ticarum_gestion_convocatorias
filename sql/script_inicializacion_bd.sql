-- ticarum_convocatorias · Esquema de base de datos (MariaDB/MySQL)

DROP DATABASE IF EXISTS ticarum_convocatorias;
CREATE DATABASE ticarum_convocatorias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ticarum_convocatorias;


CREATE TABLE alumnos (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email     VARCHAR(255) NOT NULL UNIQUE,
    dni       CHAR(9)      NOT NULL UNIQUE,
    CONSTRAINT chk_alumnos_dni CHECK (dni REGEXP '^[0-9]{8}[A-HJ-NP-TV-Z]$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE convocatorias (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asignatura      VARCHAR(150)      NOT NULL,
    fecha           DATE              NOT NULL,
    aula            VARCHAR(50)       NOT NULL,
    plazas_maximas  SMALLINT UNSIGNED NOT NULL,
    -- plazas_ocupadas es mantenida por los triggers de inscripciones.
    plazas_ocupadas SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    -- Checks para garantizar rangos, incluso si fallara la lógica.
    CHECK (plazas_maximas > 0),
    CHECK (plazas_ocupadas <= plazas_maximas),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE inscripciones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id       INT UNSIGNED NOT NULL,
    convocatoria_id INT UNSIGNED NOT NULL,
    -- impide doble inscripción del mismo alumno en una misma convocatoria.
    UNIQUE KEY uk_alumno_convocatoria (alumno_id, convocatoria_id),
    -- impide borrar alumno/convocatoria con inscripciones.
    FOREIGN KEY (alumno_id)       REFERENCES alumnos(id)       ON DELETE RESTRICT,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Triggers que mantienen sincronizada plazas_ocupadas.
CREATE TRIGGER trg_inscripciones_after_insert
AFTER INSERT ON inscripciones
FOR EACH ROW
UPDATE convocatorias
   SET plazas_ocupadas = plazas_ocupadas + 1
 WHERE id = NEW.convocatoria_id;

CREATE TRIGGER trg_inscripciones_after_delete
AFTER DELETE ON inscripciones
FOR EACH ROW
UPDATE convocatorias
   SET plazas_ocupadas = plazas_ocupadas - 1
 WHERE id = OLD.convocatoria_id;


-- Vista para el listado: mediante cálculos añade plazas_disponibles y estado.
CREATE VIEW v_convocatorias AS
SELECT id, asignatura, fecha, aula, plazas_maximas, plazas_ocupadas,
       (plazas_maximas - plazas_ocupadas) AS plazas_disponibles,
       CASE
           WHEN fecha < CURDATE()                 THEN 'finalizada'
           WHEN plazas_ocupadas >= plazas_maximas THEN 'completa'
           ELSE                                        'abierta'
       END AS estado
FROM convocatorias;


-- Datos de prueba (con casos para probar validación)
INSERT INTO alumnos (nombre, apellidos, email, dni) VALUES
    ('María',  'García López',     'maria.garcia@test.com',     '12345678Z'),
    ('Juan',   'Martínez Ruiz',    'juan.martinez@test.com',    '23456789P'),
    ('Laura',  'Sánchez Pérez',    'laura.sanchez@test.com',    '34567890V'),
    ('Carlos', 'Rodríguez Gómez',  'carlos.rodriguez@test.com', '45678901G'),
    ('Ana',    'Fernández Torres', 'ana.fernandez@test.com',    '56789012X'),
    ('David',  'López Jiménez',    'david.lopez@test.com',      '67890123X'),
    ('Elena',  'Pérez Hernández',  'elena.perez@test.com',      '78901234D'),
    ('Miguel', 'Díaz Moreno',      'miguel.diaz@test.com',      '89012345E'),
    ('Carmen',    'Ruiz Álvarez',      'carmen.ruiz@test.com',          '90123456A'),
    ('Pablo',     'Ortega Castro',    'pablo.ortega@test.com',         '11223344X'),
    ('Sara',      'López Martín',     'sara.lopez@test.com',           '22334455B'),
    ('Andrés',    'Navarro Flores',   'andres.navarro@test.com',       '33445566C'),
    ('Isabel',    'Moreno García',    'isabel.moreno@test.com',        '44556677D'),
    ('Francisco', 'Torres Luna',      'francisco.torres@test.com',     '55667788E'),
    ('Patricia',  'Vega Jiménez',     'patricia.vega@test.com',        '66778899F'),
    ('Alejandro', 'Castillo Ramos',   'alejandro.castillo@test.com',   '77889900G'),
    ('Natalia',   'Blanco Herrero',   'natalia.blanco@test.com',       '88990011H'),
    ('Roberto',   'Serrano Muñoz',    'roberto.serrano@test.com',      '99001122J'),
    ('Lucía',     'Guerrero Santos',  'lucia.guerrero@test.com',       '10203040K'),
    ('Tomás',     'Iglesias Rubio',   'tomas.iglesias@test.com',       '20304050L');


INSERT INTO convocatorias (asignatura, fecha, aula, plazas_maximas) VALUES
    ('Cálculo I',                '2026-10-15', 'Aula A1',   30),  -- mismo día que id=2
    ('Álgebra Lineal',           '2026-10-15', 'Aula B2',   25),  -- mismo día que id=1
    ('Programación II',          '2026-09-20', 'Aula Lab1',  3),  -- se llena con 3 alumnos
    ('Bases de Datos',           '2026-12-05', 'Aula A2',   40),
    ('Sistemas Operativos',      '2025-11-10', 'Aula B1',   30),  -- FECHA PASADA
    ('Redes de Computadores',    '2026-09-15', 'Aula Lab2', 20),
    ('Inteligencia Artificial',       '2026-11-25', 'Aula A3',    35),
    ('Estadística',                   '2026-11-20', 'Aula C1',    30),
    ('Física I',                      '2026-10-10', 'Aula A4',    25),
    ('Química General',               '2026-09-25', 'Aula Lab3',  20),
    ('Economía de Empresa',           '2025-03-10', 'Aula B3',    35),  -- FECHA PASADA
    ('Derecho Mercantil',             '2026-12-15', 'Aula C2',    40),
    ('Matemáticas Discretas',         '2026-10-20', 'Aula A5',    28),
    ('Arquitectura de Computadores',  '2026-11-05', 'Aula Lab4',  15),
    ('Inglés Técnico',                '2026-09-30', 'Aula B4',    50),
    ('Ética Profesional',             '2025-06-15', 'Aula C3',    25),  -- FECHA PASADA
    ('Diseño de Interfaces',          '2026-12-01', 'Aula Lab5',  20);

INSERT INTO inscripciones (alumno_id, convocatoria_id) VALUES
    (1, 1), (2, 2), (3, 1),
    (4, 3), (5, 3), (6, 3),   -- Programación II completa (Completa)
    (7, 4), (8, 7);