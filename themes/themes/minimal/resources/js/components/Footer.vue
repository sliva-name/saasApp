<template>
  <footer class="site-footer">
    <div class="footer-container">
      <!-- Main Footer Content -->
      <div class="footer-content">
        <!-- Company Info -->
        <div class="footer-section">
          <h3 class="footer-title">{{ branding.site_name || 'Store' }}</h3>
          <p class="footer-description">
            {{ branding.description || 'Your trusted e-commerce partner' }}
          </p>
          <div class="social-links">
            <a v-if="themeConfig.social?.facebook" :href="themeConfig.social.facebook" class="social-link">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a v-if="themeConfig.social?.twitter" :href="themeConfig.social.twitter" class="social-link">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a v-if="themeConfig.social?.instagram" :href="themeConfig.social.instagram" class="social-link">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
          <h3 class="footer-title">Quick Links</h3>
          <ul class="footer-links">
            <li><router-link to="/" class="footer-link">Home</router-link></li>
            <li><router-link to="/products" class="footer-link">Products</router-link></li>
            <li><router-link to="/categories" class="footer-link">Categories</router-link></li>
            <li><router-link to="/about" class="footer-link">About Us</router-link></li>
            <li><router-link to="/contact" class="footer-link">Contact</router-link></li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
          <h3 class="footer-title">Customer Service</h3>
          <ul class="footer-links">
            <li><router-link to="/help" class="footer-link">Help Center</router-link></li>
            <li><router-link to="/shipping" class="footer-link">Shipping Info</router-link></li>
            <li><router-link to="/returns" class="footer-link">Returns</router-link></li>
            <li><router-link to="/faq" class="footer-link">FAQ</router-link></li>
            <li><router-link to="/support" class="footer-link">Support</router-link></li>
          </ul>
        </div>

        <!-- Legal -->
        <div class="footer-section">
          <h3 class="footer-title">Legal</h3>
          <ul class="footer-links">
            <li><router-link to="/privacy" class="footer-link">Privacy Policy</router-link></li>
            <li><router-link to="/terms" class="footer-link">Terms of Service</router-link></li>
            <li><router-link to="/cookies" class="footer-link">Cookie Policy</router-link></li>
            <li><router-link to="/disclaimer" class="footer-link">Disclaimer</router-link></li>
          </ul>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <p class="copyright">
            © {{ new Date().getFullYear() }} {{ branding.site_name || 'Store' }}. 
            All rights reserved.
          </p>
          <div class="payment-methods">
            <span class="payment-text">We accept:</span>
            <div class="payment-icons">
              <!-- Payment method icons would go here -->
              <span class="payment-icon">💳</span>
              <span class="payment-icon">💰</span>
              <span class="payment-icon">🏦</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { useThemeConfig } from '../composables/useThemeConfig';

const { config: themeConfig, branding } = useThemeConfig();
</script>

<style scoped>
.site-footer {
  @apply bg-gray-900 text-white;
  background-color: var(--color-footer-bg, #1a202c);
}

.footer-container {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}

.footer-content {
  @apply py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8;
}

.footer-section {
  @apply space-y-4;
}

.footer-title {
  @apply text-lg font-semibold mb-4;
  color: var(--color-footer-title, #f7fafc);
}

.footer-description {
  @apply text-sm leading-relaxed;
  color: var(--color-footer-text, #a0aec0);
}

.social-links {
  @apply flex space-x-4 mt-4;
}

.social-link {
  @apply text-gray-400 hover:text-white transition-colors;
}

.footer-links {
  @apply space-y-2;
}

.footer-link {
  @apply text-sm transition-colors;
  color: var(--color-footer-link, #a0aec0);
}

.footer-link:hover {
  color: var(--color-footer-link-hover, #e2e8f0);
}

.footer-bottom {
  @apply border-t border-gray-800 py-6;
}

.footer-bottom-content {
  @apply flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0;
}

.copyright {
  @apply text-sm;
  color: var(--color-footer-text, #a0aec0);
}

.payment-methods {
  @apply flex items-center space-x-4;
}

.payment-text {
  @apply text-sm;
  color: var(--color-footer-text, #a0aec0);
}

.payment-icons {
  @apply flex space-x-2;
}

.payment-icon {
  @apply text-lg;
}
</style>
