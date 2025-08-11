import { ref, computed, watch } from 'vue'

// Simple singleton cart store persisted in localStorage
const stored = typeof window !== 'undefined' ? window.localStorage.getItem('cart') : null
const cartItems = ref(stored ? JSON.parse(stored) : [])

watch(cartItems, (val) => {
  try {
    window.localStorage.setItem('cart', JSON.stringify(val))
  } catch (_) {
    // ignore
  }
}, { deep: true })

const findIndexById = (productId) => cartItems.value.findIndex(i => i.id === productId)

export function useCart() {
  const addItem = (product, quantity = 1) => {
    const idx = findIndexById(product.id)
    if (idx !== -1) {
      cartItems.value[idx].quantity += quantity
    } else {
      cartItems.value.push({
        id: product.id,
        slug: product.slug,
        name: product.name,
        price: Number(product.price || 0),
        image_url: product.image_url || null,
        stock: typeof product.stock === 'number' ? product.stock : null,
        quantity: quantity,
      })
    }
  }

  const removeItem = (productId) => {
    const idx = findIndexById(productId)
    if (idx !== -1) cartItems.value.splice(idx, 1)
  }

  const setQuantity = (productId, quantity) => {
    const idx = findIndexById(productId)
    if (idx !== -1) {
      cartItems.value[idx].quantity = Math.max(1, quantity)
    }
  }

  const clear = () => { cartItems.value = [] }

  const count = computed(() => cartItems.value.reduce((sum, i) => sum + i.quantity, 0))
  const subtotal = computed(() => cartItems.value.reduce((sum, i) => sum + i.price * i.quantity, 0))

  return { cartItems, addItem, removeItem, setQuantity, clear, count, subtotal }
}


