SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS animethai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE animethai;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    name_th VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_th VARCHAR(255) NOT NULL,
    description TEXT,
    thumbnail VARCHAR(255) DEFAULT NULL,
    category_id INT DEFAULT NULL,
    status ENUM('ongoing','completed') DEFAULT 'ongoing',
    year INT DEFAULT NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    episode_number INT NOT NULL,
    episode_title VARCHAR(255) DEFAULT NULL,
    subtitle_type ENUM('sub','dub','both') DEFAULT 'sub',
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY uk_movie_ep (movie_id, episode_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE video_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    episode_id INT NOT NULL,
    server_name VARCHAR(100) NOT NULL,
    embed_url TEXT NOT NULL,
    quality ENUM('480p','720p','1080p') DEFAULT '720p',
    sort_order INT DEFAULT 0,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) DEFAULT '#',
    position ENUM('left','right','top','bottom') NOT NULL,
    active TINYINT DEFAULT 1,
    expire_date DATE DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories (8)
INSERT INTO categories (name, name_th) VALUES
('Action', 'แอ็คชั่น'),
('Adventure', 'ผจญภัย'),
('Comedy', 'ตลก'),
('Drama', 'ดราม่า'),
('Fantasy', 'แฟนตาซี'),
('Romance', 'โรแมนติก'),
('Sci-Fi', 'ไซไฟ'),
('Horror', 'สยองขวัญ');

-- Admin (admin / admin1234)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@animethai.local', '$2y$10$jMXKY3xqv1wBUtAV9157p.5oiJs0NjBNRq2QkKbYoxICH7f7j4fie', 'admin');

-- Movies (6)
INSERT INTO movies (title, title_th, description, thumbnail, category_id, status, year, views) VALUES
('Demon Slayer', 'ดาบพิฆาตอสูร', 'ทันจิโร่ ออกเดินทางเพื่อแก้แค้นให้ครอบครัวและช่วยน้องสาวที่กลายเป็นอสูร', 'https://picsum.photos/seed/ds/300/400', 1, 'ongoing', 2019, 15420),
('One Piece', 'วันพีซ', 'การผจญภัยของลูฟี่และพรรคโจรสลัดหมวกฟาง เพื่อค้นหาสมบัติ One Piece', 'https://picsum.photos/seed/op/300/400', 2, 'ongoing', 1999, 28900),
('Spy x Family', 'สไป x แฟมิลี่', 'สายลับต้องสร้างครอบครัวปลอมเพื่อปฏิบัติภารกิจ', 'https://picsum.photos/seed/sxf/300/400', 3, 'completed', 2022, 12300),
('Attack on Titan', 'ผ่าพิภพไททัน', 'มนุษยชาติต่อสู้กับไททันยักษ์เพื่อความอยู่รอด', 'https://picsum.photos/seed/aot/300/400', 1, 'completed', 2013, 35000),
('Jujutsu Kaisen', 'มหาเวทย์ผนึกกาตอน', 'ยูจิ เข้าร่วมโรงเรียนไม้กายสิทธิ์เพื่อต่อสู้คำสาป', 'https://picsum.photos/seed/jjk/300/400', 1, 'ongoing', 2020, 18700),
('Your Name', 'หลับใหล ฝันถึงชื่อเธอ', 'เด็กหนุ่มและสาวที่สลับร่างกันในฝัน', 'https://picsum.photos/seed/yn/300/400', 4, 'completed', 2016, 8900);

-- Episodes (3 per movie)
INSERT INTO episodes (movie_id, episode_number, episode_title, subtitle_type) VALUES
(1,1,'ตอนที่ 1 ทันจิโร่','sub'),(1,2,'ตอนที่ 2 การฝึก','sub'),(1,3,'ตอนที่ 3 การสอบ','both'),
(2,1,'ตอนที่ 1 โจรสลัดหมวกฟาง','sub'),(2,2,'ตอนที่ 2 โซโล','dub'),(2,3,'ตอนที่ 3 นามิ','sub'),
(3,1,'ตอนที่ 1 ภารกิจ','sub'),(3,2,'ตอนที่ 2 โรงเรียน','both'),(3,3,'ตอนที่ 3 สอบ','sub'),
(4,1,'ตอนที่ 1 กำแพง','sub'),(4,2,'ตอนที่ 2 การฝึก','sub'),(4,3,'ตอนที่ 3 ไททัน','both'),
(5,1,'ตอนที่ 1 คำสาป','sub'),(5,2,'ตอนที่ 2 โรงเรียน','dub'),(5,3,'ตอนที่ 3 ภารกิจ','sub'),
(6,1,'ตอนที่ 1 ฝัน','sub'),(6,2,'ตอนที่ 2 สลับร่าง','sub'),(6,3,'ตอนที่ 3 ความจริง','both');

-- Video sources (3 servers per episode)
INSERT INTO video_sources (episode_id, server_name, embed_url, quality, sort_order)
SELECT e.id, s.server_name, s.embed_url, s.quality, s.sort_order
FROM episodes e
CROSS JOIN (
    SELECT 'Streamtape' AS server_name, 'https://www.youtube.com/embed/VQGCKyvzIM4' AS embed_url, '480p' AS quality, 1 AS sort_order
    UNION SELECT 'Doodstream', 'https://www.youtube.com/embed/VQGCKyvzIM4', '720p', 2
    UNION SELECT 'Google Drive', 'https://www.youtube.com/embed/VQGCKyvzIM4', '1080p', 3
) s;

-- Banners (4 positions)
INSERT INTO banners (image_url, link_url, position, active, expire_date) VALUES
('https://picsum.photos/seed/top/728/90', 'https://example.com', 'top', 1, '2026-12-31'),
('https://picsum.photos/seed/left/160/600', 'https://example.com', 'left', 1, '2026-12-31'),
('https://picsum.photos/seed/right/160/600', 'https://example.com', 'right', 1, '2026-12-31'),
('https://picsum.photos/seed/bottom/728/90', 'https://example.com', 'bottom', 1, '2026-12-31');
