# โปรเจกต์ `products-api`

โปรเจกต์นี้คือ Backend API สำหรับจัดการข้อมูลสินค้า พัฒนาด้วย PHP (Laravel 12)

## รายละเอียด

-   **Service**: PHP-FPM 8.2
-   **Framework**: Laravel 12
-   **Container Name**: `products-api-container`
-   **Port (Internal)**: `9000` (สำหรับเชื่อมต่อกับ Gateway ผ่าน FastCGI)
-   **ฐานข้อมูล**: เชื่อมต่อกับ `products-db-container`

## ขั้นตอนการติดตั้งและใช้งาน

**สำคัญ:** คุณต้องรัน `products-db` ก่อน และต้องสร้าง Docker Network ชื่อ `product-demo-network` (ดูขั้นตอนได้จาก `README.md` หลัก)

1.  **Install Dependencies:**
    - เนื่องจากเรา Mount volume จากเครื่องเราเข้าไปใน Container เราต้องรัน `composer install` จากใน Container เพื่อติดตั้ง Dependencies ต่างๆ
    - เริ่ม Container ในโหมด background ก่อน:
      ```bash
      docker-compose up -d
      ```
    - จากนั้น รัน `composer install`:
      ```bash
      docker-compose exec api composer install
      ```

2.  **Run Database Migrations & Seeding:**
    - เมื่อ Container ทำงานแล้ว ให้รันคำสั่งเพื่อสร้างตารางข้อมูลและเพิ่มข้อมูลตัวอย่าง:
      ```bash
      docker-compose exec api php artisan migrate --seed
      ```

3.  **เข้าถึง API:**
    - โดยปกติ API จะถูกเรียกผ่าน Gateway ที่ `http://api.products.local:8010/api/products`
    - หากต้องการทดสอบโดยตรง (ต้องเปิด port 8000 ใน `docker-compose.yml` ก่อน) สามารถทำได้โดยรันคำสั่ง `php artisan serve` ใน container

## การหยุดการทำงาน

-   เมื่อต้องการหยุด Container:
    ```bash
    docker-compose down
    ```

## Endpoints

-   `GET /api/products`: ดึงข้อมูลสินค้าทั้งหมด
-   `GET /api/products/{id}`: ดึงข้อมูลสินค้าตาม ID
-   `POST /api/products`: สร้างสินค้าใหม่
-   `PUT /api/products/{id}`: อัปเดตข้อมูลสินค้า
-   `DELETE /api/products/{id}`: ลบสินค้า