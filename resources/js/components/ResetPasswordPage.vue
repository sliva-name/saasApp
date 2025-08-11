<template>
    <section>
        <div class="max-w-lg mx-auto">
            <div class="bg-white border border-secondary-200 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-secondary-200 bg-gradient-to-r from-secondary-50 to-primary-50">
                    <h1 class="text-2xl sm:text-3xl font-bold text-secondary-900">Сброс пароля</h1>
                    <p class="mt-1 text-secondary-600">Введите новый пароль</p>
                </div>
                <form class="p-6 sm:p-8 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Email</label>
                        <input v-model="form.email" type="email" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="username" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Новый пароль</label>
                        <input v-model="form.password" type="password" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="new-password" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Подтверждение</label>
                        <input v-model="form.password_confirmation" type="password" class="mt-1 w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" autocomplete="new-password" required />
                    </div>

                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

                    <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors" :disabled="submitting">
                        <span v-if="!submitting">Сбросить пароль</span>
                        <span v-else>Сбрасываем…</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
    props: { token: { type: String, required: true } },
    setup(props) {
        const form = ref({ email: '', password: '', password_confirmation: '' })
        const submitting = ref(false)
        const error = ref('')

        const submit = async () => {
            error.value = ''
            submitting.value = true
            try {
                const { data: csrf } = await axios.get('/api/csrf-token')
                const token = csrf?.token
                await axios.post('/reset-password', {
                    token: props.token,
                    email: form.value.email,
                    password: form.value.password,
                    password_confirmation: form.value.password_confirmation,
                    _token: token,
                })
                window.location.href = '/login'
            } catch (e) {
                if (e.response && e.response.status === 422) {
                    const message = e.response.data?.message || 'Ошибка валидации'
                    error.value = message
                } else {
                    error.value = 'Не удалось сбросить пароль. Попробуйте позже.'
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


