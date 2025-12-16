# โปรเจกต์ `products-db`

โปรเจกต์นี้ทำหน้าที่ให้บริการฐานข้อมูล MySQL 8.0 สำหรับโปรเจกต์ทั้งหมด

## รายละเอียด

-   **Service**: MySQL 8.0
-   **Container Name**: `products-db-container`
-   **Database**: `products`
-   **User**: `root`
-   **Password**: `1234`
-   **Port**: `3310` (ภายนอก) -> `3306` (ภายใน Container)
-   **Data Volume**: `mysql_data` (เพื่อเก็บข้อมูลแม้ Container จะถูกลบ)

## ขั้นตอนการติดตั้งและใช้งาน

**สำคัญ:** คุณต้องสร้าง Docker Network ชื่อ `product-demo-network` ก่อนที่จะรันโปรเจกต์นี้ (ดูขั้นตอนได้จาก `README.md` หลัก)

1.  เปิด Terminal
2.  เข้ามาที่ไดเรกทอรีนี้ (`/products-db`)
3.  รันคำสั่งเพื่อเริ่มฐานข้อมูลในโหมด background:

    ```bash
    docker-compose up -d
    ```

4.  เมื่อต้องการหยุดการทำงานของฐานข้อมูล:

    ```bash
    docker-compose down
    ```

## การตรวจสอบสถานะ

คุณสามารถตรวจสอบว่า Container ทำงานอยู่หรือไม่ด้วยคำสั่ง:

```bash
docker ps
```

คุณควรจะเห็น `products-db-container` อยู่ในรายการ
