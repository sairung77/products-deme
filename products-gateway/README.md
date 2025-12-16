# โปรเจกต์ `products-gateway`

โปรเจกต์นี้คือ Reverse Proxy ที่ทำหน้าที่เป็นประตูหน้า (Gateway) สำหรับระบบทั้งหมด สร้างด้วย Nginx

## รายละเอียด

-   **Service**: Nginx
-   **Container Name**: `products-gateway-container`
-   **Port**: `8010` (ภายนอก) -> `80` (ภายใน Container)

## กฎการ Routing

Gateway นี้จะจัดการเส้นทาง 2 รูปแบบหลัก:

1.  **`http://www.products.local:8010`**
    -   ทุก request ที่เข้ามาที่นี่จะถูกส่งต่อ (Proxy Pass) ไปยัง `products-ui-container` ที่ Port `5173` (Vite Dev Server)
    -   มีการตั้งค่า Header เพิ่มเติมเพื่อให้ Hot-Reload ของ Vite ทำงานผ่าน Proxy ได้

2.  **`http://api.products.local:8010`**
    -   ทุก request ที่เข้ามาที่นี่จะถูกส่งต่อไปยัง `products-api-container`
    -   การเชื่อมต่อนี้ใช้ `fastcgi_pass` เพื่อส่งต่อสคริปต์ PHP ไปให้ PHP-FPM ประมวลผลโดยตรง
    -   เพื่อให้ทำงานได้ เราจึงจำเป็นต้อง Mount source code ของ `products-api` เข้ามาใน Gateway Container นี้ด้วย

## ขั้นตอนการติดตั้งและใช้งาน

**สำคัญ:** คุณต้องรัน `products-api` และ `products-ui` ก่อน และต้องสร้าง Docker Network ชื่อ `product-demo-network` (ดูขั้นตอนได้จาก `README.md` หลัก)

1.  เปิด Terminal
2.  เข้ามาที่ไดเรกทอรีนี้ (`/products-gateway`)
3.  รันคำสั่งเพื่อเริ่ม Gateway:
    ```bash
    docker-compose up -d
    ```

## การหยุดการทำงาน

-   เมื่อต้องการหยุด Container:
    ```bash
    docker-compose down
    ```
