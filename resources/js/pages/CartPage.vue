<template>
  <section>
    <div class="max-w-5xl mx-auto">
      <div class="bg-white border border-secondary-200 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-secondary-200 bg-gradient-to-r from-secondary-50 to-primary-50">
          <h1 class="text-2xl sm:text-3xl font-bold text-secondary-900">Корзина</h1>
          <p class="mt-1 text-secondary-600">Проверьте товары перед оформлением заказа</p>
        </div>

        <div class="p-6 sm:p-8">
          <!-- Empty state -->
          <div v-if="items.length === 0" class="text-center py-16">
            <div class="w-16 h-16 mx-auto mb-4 bg-secondary-100 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-secondary-900 mb-2">Ваша корзина пуста</h3>
            <p class="text-secondary-600">Добавьте товары из каталога</p>
            <RouterLink to="/" class="inline-flex items-center mt-6 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">В каталог</RouterLink>
          </div>

          <!-- Cart content -->
          <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items list -->
            <div class="lg:col-span-2 space-y-4">
              <div v-for="item in items" :key="item.id" class="flex items-center gap-4 p-4 border border-secondary-200 rounded-xl hover:shadow-soft transition-shadow">
                <img :src="item.image_url || '/images/placeholder.png'" :alt="item.name" class="w-20 h-20 object-cover rounded-lg flex-shrink-0" />
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                      <RouterLink :to="`/product/${item.slug}`" class="font-medium text-secondary-900 hover:text-primary-600 line-clamp-1">{{ item.name }}</RouterLink>
                      <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="text-secondary-600">Цена за шт:</span>
                        <span class="font-medium text-secondary-900">{{ formatPrice(item.price) }}</span>
                        <span v-if="item.stock != null" class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full text-[10px]"
                              :class="item.stock > 0 ? 'bg-green-100 text-green-700' : 'bg-secondary-100 text-secondary-600'">
                          <span class="w-1.5 h-1.5 rounded-full" :class="item.stock > 0 ? 'bg-green-500' : 'bg-secondary-400'"></span>
                          {{ item.stock > 0 ? 'В наличии' : 'Нет в наличии' }}
                        </span>
                      </div>
                    </div>
                    <button @click="remove(item.id)" class="text-secondary-400 hover:text-red-600 flex-shrink-0" aria-label="Удалить">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                  <div class="mt-3 flex items-center justify-between gap-4 flex-wrap">
                    <div class="inline-flex items-center rounded-lg border border-secondary-300 overflow-hidden">
                      <button @click="dec(item.id, item.quantity)" :disabled="item.quantity <= 1" class="h-9 w-9 grid place-items-center text-secondary-700 hover:bg-secondary-100 disabled:opacity-40 disabled:cursor-not-allowed" title="Уменьшить">−</button>
                      <input type="number" min="1" :value="item.quantity" @input="onQtyInput(item.id, $event.target.value)" class="w-12 h-9 text-center outline-none" />
                      <button @click="inc(item.id)" :disabled="item.stock != null && item.quantity >= item.stock" class="h-9 w-9 grid place-items-center text-secondary-700 hover:bg-secondary-100 disabled:opacity-40 disabled:cursor-not-allowed" title="Увеличить">＋</button>
                    </div>
                    <div v-if="item.stock != null && item.quantity >= item.stock" class="text-xs text-secondary-500">Макс: {{ item.stock }}</div>
                    <div class="text-right ml-auto">
                      <div class="text-xs text-secondary-600">Сумма</div>
                      <div class="text-base font-semibold text-secondary-900">{{ formatPrice(item.price * item.quantity) }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-between pt-2">
                <RouterLink to="/" class="text-primary-600 hover:text-primary-700 font-medium">← Продолжить покупки</RouterLink>
                <button @click="clear()" class="text-secondary-600 hover:text-secondary-800">Очистить корзину</button>
              </div>
            </div>

            <!-- Summary -->
            <aside class="lg:col-span-1">
              <div class="lg:sticky lg:top-24 border border-secondary-200 rounded-xl p-4 space-y-4">
                <!-- Free shipping banner -->
                <div class="p-3 rounded-lg" :class="freeShippingReached ? 'bg-green-50 text-green-700' : 'bg-secondary-50 text-secondary-700'">
                  <span v-if="freeShippingReached">Доставка бесплатно 🎉</span>
                  <span v-else>До бесплатной доставки осталось {{ formatPrice(Math.max(0, freeShippingThreshold - subtotal)) }}</span>
                  <div class="mt-2 h-2 bg-secondary-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500" :style="{ width: progress + '%' }"></div>
                  </div>
                </div>

                <!-- Coupon -->
                <div class="flex gap-2">
                  <input v-model="couponCode" placeholder="Промокод" class="flex-1 px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
                  <button @click="applyCoupon" class="px-3 py-2 bg-secondary-100 text-secondary-800 rounded-lg hover:bg-secondary-200">Применить</button>
                </div>
                <p v-if="couponMessage" class="text-xs" :class="couponApplied ? 'text-green-700' : 'text-red-600'">{{ couponMessage }}</p>

                <div class="space-y-2 text-sm">
                  <div class="flex justify-between text-secondary-700">
                    <span>Товары, {{ count }}</span>
                    <span>{{ formatPrice(subtotal) }}</span>
                  </div>
                  <div class="flex justify-between text-secondary-700" v-if="discount > 0">
                    <span>Скидка ({{ Math.round(discountPercent * 100) }}%)</span>
                    <span>-{{ formatPrice(discount) }}</span>
                  </div>
                  <div class="flex justify-between text-secondary-700">
                    <span>Доставка</span>
                    <span>{{ shippingCost === 0 ? 'Бесплатно' : formatPrice(shippingCost) }}</span>
                  </div>
                  <div class="flex justify-between font-semibold text-secondary-900 text-base pt-2 border-t border-secondary-200">
                    <span>Итого</span>
                    <span>{{ formatPrice(total) }}</span>
                  </div>
                </div>
                <button class="w-full mt-2 px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Перейти к оформлению</button>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { RouterLink } from 'vue-router'
import { computed, ref } from 'vue'
import { useCart } from '@/stores/cart'

export default {
  components: { RouterLink },
  setup() {
    const { cartItems, setQuantity, removeItem, clear, count, subtotal } = useCart()

    const items = cartItems
    const formatPrice = (price) => new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', minimumFractionDigits: 0 }).format(price)

    const onQtyInput = (id, value) => {
      const qty = parseInt(value || '1', 10)
      setQuantity(id, isNaN(qty) ? 1 : qty)
    }

    const inc = (id) => {
      const item = items.value.find(i => i.id === id)
      setQuantity(id, (item?.quantity || 1) + 1)
    }

    const dec = (id, current) => {
      setQuantity(id, Math.max(1, (current || 1) - 1))
    }

    const remove = (id) => removeItem(id)

    // Promo and totals
    const couponCode = ref('')
    const couponApplied = ref(false)
    const couponMessage = ref('')
    const discountPercent = ref(0)
    const freeShippingThreshold = 5000
    const shippingCost = computed(() => (subtotal.value >= freeShippingThreshold ? 0 : (subtotal.value > 0 ? 300 : 0)))
    const discount = computed(() => Math.round((subtotal.value * discountPercent.value)))
    const total = computed(() => Math.max(0, subtotal.value + shippingCost.value - discount.value))
    const freeShippingReached = computed(() => shippingCost.value === 0 && subtotal.value > 0)
    const progress = computed(() => subtotal.value > 0 ? Math.min(100, Math.round((subtotal.value / freeShippingThreshold) * 100)) : 0)

    const applyCoupon = () => {
      const code = couponCode.value.trim().toUpperCase()
      if (!code) {
        couponApplied.value = false
        couponMessage.value = 'Введите промокод'
        return
      }
      if (code === 'SALE10') {
        discountPercent.value = 0.10
        couponApplied.value = true
        couponMessage.value = 'Промокод применён: скидка 10%'
      } else if (code === 'SALE5') {
        discountPercent.value = 0.05
        couponApplied.value = true
        couponMessage.value = 'Промокод применён: скидка 5%'
      } else {
        discountPercent.value = 0
        couponApplied.value = false
        couponMessage.value = 'Неверный промокод'
      }
    }

    return { items, formatPrice, onQtyInput, inc, dec, remove, clear, count, subtotal, couponCode, couponApplied, couponMessage, discountPercent, freeShippingThreshold, shippingCost, discount, total, freeShippingReached, progress, applyCoupon }
  }
}
</script>

<style scoped>
.shadow-soft { box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06) }
</style>


