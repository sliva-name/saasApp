<template>
    <section>
        <div class="bg-white border border-secondary-200 rounded-2xl shadow-soft overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-secondary-200 bg-gradient-to-r from-secondary-50 to-primary-50">
                <h1 class="text-2xl sm:text-3xl font-bold text-secondary-900">
                    Аккаунт
                </h1>
                <p class="mt-1 text-secondary-600">Управление профилем и заказами</p>
            </div>

            <div class="p-6 sm:p-8">
                <div v-if="loading" class="flex items-center space-x-3 text-secondary-600">
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary-500 border-t-transparent"></div>
                    <span>Загрузка данных аккаунта…</span>
                </div>

                <div v-else>
                    <div v-if="user" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <aside class="lg:col-span-1 space-y-4">
                            <div class="bg-secondary-50 border border-secondary-200 rounded-xl p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0 17.93 17.93 0 0 1-7.5 1.63 17.93 17.93 0 0 1-7.5-1.63Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-secondary-500">Вы вошли как</p>
                                        <p class="text-lg font-semibold text-secondary-900">{{ user.name }}</p>
                                        <p class="text-sm text-secondary-600">{{ user.email }}</p>
                                    </div>
                                </div>
                            </div>
                            <nav class="space-y-2">
                                <button class="w-full text-left px-4 py-2 rounded-lg border border-secondary-200 hover:bg-secondary-50 transition-colors font-medium">Профиль</button>
                                <button class="w-full text-left px-4 py-2 rounded-lg border border-secondary-200 hover:bg-secondary-50 transition-colors">Мои заказы</button>
                                <button class="w-full text-left px-4 py-2 rounded-lg border border-secondary-200 hover:bg-secondary-50 transition-colors">Адреса доставки</button>
                                <button class="w-full text-left px-4 py-2 rounded-lg border border-secondary-200 hover:bg-secondary-50 transition-colors">Настройки</button>
                            </nav>
                        </aside>

                        <div class="lg:col-span-2 space-y-8">
                            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                                <h2 class="text-xl font-semibold text-secondary-900">Данные профиля</h2>
                                <p class="text-sm text-secondary-600 mt-1">Измените ваши персональные данные</p>

                                <form class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4" @submit.prevent>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700">Имя</label>
                                        <input type="text" v-model="form.name" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700">Email</label>
                                        <input type="email" v-model="form.email" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                    </div>
                                    <div class="sm:col-span-2 flex justify-end">
                                        <button type="button" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors" @click="saveProfile" :disabled="saving">
                                            <span v-if="!saving">Сохранить</span>
                                            <span v-else>Сохранение…</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                                <h2 class="text-xl font-semibold text-secondary-900">Безопасность</h2>
                                <p class="text-sm text-secondary-600 mt-1">Смените пароль аккаунта</p>
                                <form class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4" @submit.prevent>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700">Новый пароль</label>
                                        <input type="password" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" v-model="password.new" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700">Подтверждение</label>
                                        <input type="password" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" v-model="password.confirm" />
                                    </div>
                                    <div class="sm:col-span-2 flex justify-end">
                                        <button type="button" class="px-4 py-2 bg-secondary-100 text-secondary-800 rounded-lg hover:bg-secondary-200 transition-colors" @click="changePassword" :disabled="saving">
                                            Обновить пароль
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <div class="mx-auto w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center">
                            <svg class="h-8 w-8 text-secondary-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0 17.93 17.93 0 0 1-7.5 1.63 17.93 17.93 0 0 1-7.5-1.63Z" />
                            </svg>
                        </div>
                        <h2 class="mt-6 text-xl font-semibold text-secondary-900">Войдите в аккаунт</h2>
                        <p class="mt-2 text-secondary-600">Чтобы управлять профилем и заказывать товары</p>
                        <div class="mt-6 flex justify-center space-x-3">
                            <a href="/login" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">Войти</a>
                            <a href="/register" class="px-4 py-2 bg-secondary-100 text-secondary-800 rounded-lg hover:bg-secondary-200 transition-colors">Регистрация</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
    setup() {
        const loading = ref(true)
        const user = ref(null)
        const saving = ref(false)
        const form = ref({ name: '', email: '' })
        const password = ref({ new: '', confirm: '' })

        const loadMe = async () => {
            loading.value = true
            try {
                const { data } = await axios.get('/api/me')
                user.value = data || null
                if (user.value) {
                    form.value.name = user.value.name || ''
                    form.value.email = user.value.email || ''
                }
            } catch (e) {
                user.value = null
            } finally {
                loading.value = false
            }
        }

        const saveProfile = async () => {
            if (!user.value) return
            saving.value = true
            try {
                await axios.put('/user/profile-information', {
                    name: form.value.name,
                    email: form.value.email,
                })
            } catch (e) {
                // noop: можно добавить уведомления
            } finally {
                saving.value = false
                await loadMe()
            }
        }

        const changePassword = async () => {
            if (!user.value) return
            if (!password.value.new || password.value.new !== password.value.confirm) return
            saving.value = true
            try {
                await axios.put('/user/password', {
                    current_password: '',
                    password: password.value.new,
                    password_confirmation: password.value.confirm,
                })
                password.value.new = ''
                password.value.confirm = ''
            } catch (e) {
                // noop
            } finally {
                saving.value = false
            }
        }

        onMounted(loadMe)

        return { loading, user, form, password, saving, saveProfile, changePassword }
    }
}
</script>

<style scoped>
.shadow-soft { box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06) }
.animate-fade-in { animation: fade-in 0.3s ease } 
@keyframes fade-in { from { opacity: 0; transform: translateY(8px) } to { opacity: 1; transform: translateY(0) } }
</style>


