<template>
    <section>
        <div class="max-w-lg mx-auto">
            <div class="bg-white border border-secondary-200 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-secondary-200 bg-gradient-to-r from-secondary-50 to-primary-50">
                    <h1 class="text-2xl sm:text-3xl font-bold text-secondary-900">Вход</h1>
                    <p class="mt-1 text-secondary-600">Войдите в аккаунт, чтобы продолжить</p>
                </div>
                <form class="p-6 sm:p-8 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Email</label>
                        <input v-model="form.email" type="email" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="username" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Пароль</label>
                        <input v-model="form.password" type="password" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="current-password" required />
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center space-x-2 text-sm text-secondary-700">
                            <input v-model="form.remember" type="checkbox" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500" />
                            <span>Запомнить меня</span>
                        </label>
                        <a href="/forgot-password" class="text-sm text-primary-600 hover:text-primary-700">Забыли пароль?</a>
                    </div>

                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

                    <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors" :disabled="submitting">
                        <span v-if="!submitting">Войти</span>
                        <span v-else>Входим…</span>
                    </button>

                    <p class="text-center text-sm text-secondary-600">Нет аккаунта?
                        <a href="/register" class="text-primary-600 hover:text-primary-700 font-medium">Зарегистрироваться</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
    
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
    setup() {
        const form = ref({ email: '', password: '', remember: false })
        const submitting = ref(false)
        const error = ref('')

        const submit = async () => {
            error.value = ''
            submitting.value = true
            try {
                // Получаем CSRF токен
                const { data: csrf } = await axios.get('/api/csrf-token')
                const token = csrf?.token
                await axios.post('/login', {
                    email: form.value.email,
                    password: form.value.password,
                    remember: form.value.remember,
                    _token: token,
                })
                window.location.href = '/account'
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    const message = e.response.data?.message || 'Неверные учетные данные'
                    error.value = message
                } else {
                    error.value = 'Не удалось выполнить вход. Попробуйте позже.'
                }
            } finally {
                submitting.value = false
            }
        }

        return { form, submitting, error, submit }
    }
}
</script>

<style scoped>
.shadow-soft { box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06) }
</style>


