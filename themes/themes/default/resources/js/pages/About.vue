<template>
  <div class="about-page">
    <div class="hero-section bg-gradient-to-r from-blue-500 to-purple-600 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">About Us</h1>
        <p class="text-xl max-w-3xl mx-auto">
          We're passionate about providing the best e-commerce experience
        </p>
      </div>
    </div>

    <div class="content-section py-16">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-3xl font-bold mb-6">Our Story</h2>
            <p class="text-gray-600 mb-4">
              Founded with a vision to make online shopping accessible and enjoyable for everyone, 
              we've been serving customers with dedication and innovation.
            </p>
            <p class="text-gray-600 mb-4">
              Our team is committed to providing high-quality products, exceptional customer service, 
              and a seamless shopping experience across all devices.
            </p>
            <p class="text-gray-600">
              We believe in the power of technology to connect people and create meaningful experiences 
              through commerce.
            </p>
          </div>
          <div class="text-center">

          </div>
        </div>
      </div>
    </div>

    <div class="stats-section bg-gray-50 py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Achievements</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">1000+</div>
            <div class="text-gray-600">Happy Customers</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">500+</div>
            <div class="text-gray-600">Products</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">5</div>
            <div class="text-gray-600">Years Experience</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">24/7</div>
            <div class="text-gray-600">Support</div>
          </div>
        </div>
      </div>
    </div>

    <div class="team-section py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Quality</h3>
            <p class="text-gray-600">We are committed to delivering only the highest quality products to our customers.</p>
          </div>
          
          <div class="text-center p-6">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Innovation</h3>
            <p class="text-gray-600">We continuously innovate to provide the best shopping experience possible.</p>
          </div>
          
          <div class="text-center p-6">
            <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Community</h3>
            <p class="text-gray-600">We believe in building strong relationships with our customers and community.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// About page component
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, var(--color-primary, #3b82f6) 0%, var(--color-secondary, #8b5cf6) 100%);
}
</style>
