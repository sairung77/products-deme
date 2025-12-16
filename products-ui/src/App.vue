<script setup lang="ts">
import { ref, onMounted } from 'vue';

// --- State ---
interface Product {
  id?: number;
  name: string;
  price: number;
  description: string | null;
}

const products = ref<Product[]>([]);
const apiError = ref<string | null>(null);
const isLoading = ref<boolean>(true);

const isEditing = ref<boolean>(false);
const editingProduct = ref<Product>({ name: '', price: 0, description: null });

const API_BASE_URL = 'http://api.products.local:8010/api/products';

// --- API Functions ---
const fetchProducts = async () => {
  isLoading.value = true;
  apiError.value = null;
  try {
    const response = await fetch(API_BASE_URL);
    if (!response.ok) {
      throw new Error(`เกิดข้อผิดพลาด: ${response.statusText}`);
    }
    products.value = await response.json();
  } catch (error) {
    console.error(error);
    apiError.value = 'ไม่สามารถเชื่อมต่อ API ได้ กรุณาตรวจสอบว่า Gateway และ API service ทำงานถูกต้อง และตั้งค่า hosts file แล้ว';
  } finally {
    isLoading.value = false;
  }
};

const saveProduct = async () => {
  if (!editingProduct.value) return;

  try {
    const url = isEditing.value
      ? `${API_BASE_URL}/${editingProduct.value.id}`
      : API_BASE_URL;
    
    const method = isEditing.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(editingProduct.value),
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(JSON.stringify(errorData.errors || 'เกิดข้อผิดพลาดในการบันทึก'));
    }

    await fetchProducts(); // Refresh list
    resetForm();
  } catch (error) {
    console.error(error);
    apiError.value = (error as Error).message;
  }
};

const deleteProduct = async (id: number | undefined) => {
  if (!id || !confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')) return;

  try {
    const response = await fetch(`${API_BASE_URL}/${id}`, {
      method: 'DELETE',
    });
    if (!response.ok) {
      throw new Error('ไม่สามารถลบสินค้าได้');
    }
    await fetchProducts(); // Refresh list
  } catch (error) {
    console.error(error);
    apiError.value = (error as Error).message;
  }
};


// --- UI Functions ---
const resetForm = () => {
  isEditing.value = false;
  editingProduct.value = { name: '', price: 0, description: null };
};

const editProduct = (product: Product) => {
  isEditing.value = true;
  // Create a copy to avoid reactive mutations
  editingProduct.value = { ...product }; 
  window.scrollTo(0, 0);
};

// --- Lifecycle ---
onMounted(fetchProducts);

</script>

<template>
  <div class="app-dsv-layout">
    <header class="app-header">
      <div class="header-content">
        <h1>Products Demo</h1>
        <p>Microservices: Vue.js + Laravel + Nginx</p>
      </div>
    </header>

    <div class="main-content">
      <div v-if="isLoading" class="loading-overlay">
        <div class="spinner"></div>
        <p>Loading...</p>
      </div>

      <div v-if="apiError" class="error-toast">
        <strong>Error:</strong> {{ apiError }}
      </div>

      <div class="content-grid">
        <!-- Left Panel: Form -->
        <aside class="form-panel">
          <div class="card">
            <h2>{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
            <form @submit.prevent="saveProduct" class="product-form">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" v-model="editingProduct.name" required placeholder="Product Name" />
              </div>
              <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" v-model="editingProduct.price" required placeholder="0.00" />
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" v-model="editingProduct.description!" placeholder="Product details..."></textarea>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ isEditing ? 'Update' : 'Add Product' }}</button>
                <button type="button" @click="resetForm" v-if="isEditing" class="btn btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        </aside>

        <!-- Right Panel: List -->
        <main class="list-panel">
          <div class="card full-height">
            <div class="list-header">
              <h2>Product List</h2>
              <button @click="fetchProducts" class="btn btn-icon" title="Refresh">
                Refresh ↻
              </button>
            </div>
            
            <div class="table-container">
              <table class="product-table">
                <thead>
                  <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Name</th>
                    <th style="width: 100px;">Price</th>
                    <th>Description</th>
                    <th style="width: 140px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="products.length === 0 && !isLoading">
                    <td colspan="5" class="empty-state">No products found.</td>
                  </tr>
                  <tr v-for="product in products" :key="product.id">
                    <td>#{{ product.id }}</td>
                    <td class="font-bold">{{ product.name }}</td>
                    <td class="text-right">{{ Number(product.price).toFixed(2) }}</td>
                    <td class="text-muted">{{ product.description || '-' }}</td>
                    <td class="actions">
                      <button @click="editProduct(product)" class="btn-sm btn-edit">Edit</button>
                      <button @click="deleteProduct(product.id)" class="btn-sm btn-delete">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<style>
/* Reset & Base */
:root {
  --primary: #6366f1;
  --primary-hover: #4f46e5;
  --bg-body: #f3f4f6;
  --bg-card: #ffffff;
  --text-main: #111827;
  --text-muted: #6b7280;
  --border: #e5e7eb;
  --danger: #ef4444;
  --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

body {
  margin: 0;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  background-color: var(--bg-body);
  color: var(--text-main);
  overflow: hidden; /* Prevent body scroll */
}

/* Layout */
.app-dsv-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.app-header {
  background: var(--bg-card);
  border-bottom: 1px solid var(--border);
  padding: 1rem 2rem;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.header-content h1 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.header-content p {
  margin: 0.25rem 0 0;
  font-size: 0.875rem;
  color: var(--text-muted);
}

.main-content {
  flex: 1;
  overflow: hidden;
  padding: 1.5rem;
  position: relative;
}

.content-grid {
  display: grid;
  grid-template-columns: 350px 1fr; /* Fixed width form, flexible list */
  gap: 1.5rem;
  height: 100%;
}

/* Card */
.card {
  background: var(--bg-card);
  border-radius: 12px;
  box-shadow: var(--shadow);
  padding: 1.5rem;
  border: 1px solid var(--border);
  display: flex;
  flex-direction: column;
}

.card.full-height {
  height: 100%;
  box-sizing: border-box;
}

h2 {
  margin-top: 0;
  font-size: 1.125rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
  border-bottom: 2px solid var(--bg-body);
  padding-bottom: 0.75rem;
}

/* Form Styles */
.form-panel {
  height: 100%;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: var(--text-main);
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.625rem;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #f9fafb;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  background: white;
}

.form-group textarea {
  min-height: 100px;
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 2rem;
}

/* List Styles */
.list-panel {
  height: 100%;
  min-width: 0; /* Fix flex/grid overflow issue */
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.list-header h2 {
  margin-bottom: 0;
  border-bottom: none;
  padding-bottom: 0;
}

.table-container {
  flex: 1;
  overflow: auto; /* Scrollable table area */
  border: 1px solid var(--border);
  border-radius: 8px;
}

.product-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.product-table th {
  background: #f9fafb;
  position: sticky;
  top: 0;
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: var(--text-muted);
  font-weight: 600;
  border-bottom: 1px solid var(--border);
  z-index: 10;
}

.product-table td {
  padding: 0.875rem 1rem;
  border-bottom: 1px solid var(--border);
  font-size: 0.875rem;
  vertical-align: middle;
}

.product-table tr:last-child td {
  border-bottom: none;
}

.product-table tr:hover td {
  background-color: #f9fafb;
}

.empty-state {
  text-align: center;
  padding: 3rem !important;
  color: var(--text-muted);
}

/* Utilities */
.font-bold { font-weight: 600; }
.text-right { text-align: right; }
.text-muted { color: var(--text-muted); font-size: 0.8rem; }

/* Buttons */
.btn {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  padding: 0.625rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-primary:hover {
  background-color: var(--primary-hover);
  transform: translateY(-1px);
}

.btn-secondary {
  background-color: white;
  border: 1px solid var(--border);
  color: var(--text-main);
}

.btn-secondary:hover {
  background-color: #f9fafb;
  border-color: #d1d5db;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
}

.btn-edit {
  background-color: #eff6ff;
  color: #3b82f6;
  margin-right: 0.5rem;
}
.btn-edit:hover { background-color: #dbeafe; }

.btn-delete {
  background-color: #fef2f2;
  color: #ef4444;
}
.btn-delete:hover { background-color: #fee2e2; }

.btn-icon {
  background: none;
  border: none;
  color: var(--primary);
  cursor: pointer;
  font-size: 0.875rem;
}
.btn-icon:hover { text-decoration: underline; }

/* Feedback */
.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.8);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 50;
  font-weight: 500;
  color: var(--primary);
}

.spinner {
  width: 2rem;
  height: 2rem;
  border: 3px solid var(--border);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 0.5rem;
}

@keyframes spin { to { transform: rotate(360deg); } }

.error-toast {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #fef2f2;
  border: 1px solid #fca5a5;
  color: #991b1b;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: var(--shadow);
  z-index: 60;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { transform: translateY(-10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>