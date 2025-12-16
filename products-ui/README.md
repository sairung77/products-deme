# โปรเจกต์ `products-ui`

โปรเจกต์นี้คือ Frontend Web Application สำหรับแสดงผลและจัดการข้อมูลสินค้า พัฒนาด้วย Vue.js 3 และ TypeScript

## รายละเอียด

-   **Service**: Vue.js 3 + TypeScript (เสิร์ฟด้วย Vite สำหรับ Development และ Nginx สำหรับ Production)
-   **Container Name**: `products-ui-container`
-   **Port**: `3010` (ภายนอก) -> `5173` (ภายใน Container ของ Vite Dev Server)
-   **API Endpoint**: เรียกใช้งาน API ที่ `http://api.products.local:8010/api/products`

## การใช้งาน
 
 โปรเจกต์นี้รองรับการทำงาน 2 โหมด: **Development** (สำหรับนักพัฒนา) และ **Production** (สำหรับการใช้งานจริง)
 
 ### 1. Development Mode (Hot Reload)
 
 ใช้ `Dockerfile.dev` และ `docker-compose.dev.yml`
 *   รันด้วย **Vite Server**
 *   รองรับ **Hot Reload** (แก้โค้ดแล้วหน้าเว็บเปลี่ยนทันที)
 *   Mount volume โค้ดจากเครื่อง Host
 
 **วิธีรัน:**
 ```bash
 # ติดตั้ง dependency ครั้งแรก (ถ้ายังไม่เคย)
 docker-compose -f docker-compose.dev.yml run --rm ui npm install
 
 # รันระบบ
 docker-compose -f docker-compose.dev.yml up -d
 ```
 
 เข้าใช้งานได้ที่: `http://localhost:3010` หรือ `http://www.products.local:8010` (ผ่าน Gateway)
 
 ---
 
 ### 2. Production Mode (Optimized Static Build)
 
 ใช้ `Dockerfile` และ `docker-compose.prod.yml`
 *   ใช้ **Multi-stage build** เพื่อ compile Vue.js เป็น Static Files (HTML/CSS/JS)
 *   เสิร์ฟไฟล์ด้วย **Nginx** (เบาและเร็ว)
 *   ไม่มีการ Mount volume ของ Source code (ปลอดภัยและเป็น Immutable Infrastructure)
 *   ใช้ Port 5173 ภายใน เพื่อให้ Compatible กับ Gateway config เดิม
 
 **วิธีรัน:**
 ```bash
 docker-compose -f docker-compose.prod.yml up -d --build
 ```
 
 เข้าใช้งานได้ที่: `http://localhost:3010` หรือ `http://www.products.local:8010` (ผ่าน Gateway)
 
 ## การหยุดการทำงาน
 
 ```bash
 docker-compose down
 # หรือระบุไฟล์ถ้าจำเป็น
 docker-compose -f docker-compose.prod.yml down
 ```