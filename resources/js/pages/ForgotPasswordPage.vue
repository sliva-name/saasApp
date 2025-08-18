<template>
    <section>
        <div class="max-w-lg mx-auto">
            <div class="bg-white border border-secondary-200 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-secondary-200 bg-gradient-to-r from-secondary-50 to-primary-50">
                    <h1 class="text-2xl sm:text-3xl font-bold text-secondary-900">Восстановление пароля</h1>
                    <p class="mt-1 text-secondary-600">Мы отправим ссылку для сброса пароля на вашу почту</p>
                </div>
                <form class="p-6 sm:p-8 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Email</label>
                        <input v-model="email" type="email" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="username" required />
                    </div>

                    <p v-if="message" class="text-sm text-green-700">{{ message }}</p>
                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

                    <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors" :disabled="submitting">
                        <span v-if="!submitting">Отправить ссылку</span>
                        <span v-else>Отправляем…</span>
                    </button>

                    <p class="text-center text-sm text-secondary-600">Вспомнили пароль?
                        <a href="/login" class="text-primary-600 hover:text-primary-700 font-medium">Войти</a>
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
        const email = ref('')
        const submitting = ref(false)
        const message = ref('')
        const error = ref('')

        const submit = async () => {
            message.value = ''
            error.value = ''
            submitting.value = true
            try {
                const { data: csrf } = await axios.get('/api/csrf-token')
                const token = csrf?.token
                await axios.post('/forgot-password', { email: email.value, _token: token })
                message.value = 'Мы отправили вам письмо со ссылкой для сброса пароля.'
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    const msg = e.response.data?.message || 'Проверьте корректность email'
                    error.value = msg
                } else {
                    error.value = 'Не удалось отправить письмо. Попробуйте позже.'
                }
            } finally {
                submitting.value = false
            }
        }

        return { email, submitting, message, error, submit }
    }
}
</script>

<style scoped>
.shadow-soft { box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06) }
</style>


