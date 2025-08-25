<template>
  <div class="search-bar">
    <div class="search-container">
      <input 
        v-model="query"
        @input="handleInput"
        @keyup.enter="handleSearch"
        @keydown.down="selectNext"
        @keydown.up="selectPrev"
        @keydown.escape="hideSuggestions"
        @focus="showSuggestions = true"
        @blur="hideSuggestionsDelayed"
        type="text" 
        :placeholder="placeholder"
        class="search-input"
      />
      <button @click="handleSearch" class="search-button">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </button>
      
      <!-- Search Suggestions -->
      <div v-if="showSuggestions && suggestions.length > 0" class="suggestions-dropdown">
        <div 
          v-for="(suggestion, index) in suggestions" 
          :key="index"
          @click="selectSuggestion(suggestion)"
          :class="[
            'suggestion-item',
            { 'suggestion-active': index === selectedIndex }
          ]"
        >
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <span class="ml-2">{{ suggestion }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  placeholder: {
    type: String,
    default: 'Search products...'
  },
  modelValue: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'search']);

const query = ref(props.modelValue);
const suggestions = ref([]);
const showSuggestions = ref(false);
const selectedIndex = ref(-1);
let suggestionTimeout = null;

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  query.value = newValue;
});

const handleInput = () => {
  emit('update:modelValue', query.value);
  
  // Debounce suggestions
  clearTimeout(suggestionTimeout);
  suggestionTimeout = setTimeout(() => {
    fetchSuggestions();
  }, 300);
};

const handleSearch = () => {
  hideSuggestions();
  emit('search', query.value);
};

const fetchSuggestions = async () => {
  if (query.value.length < 2) {
    suggestions.value = [];
    return;
  }
  
  try {
    const response = await fetch(`/api/products/search?q=${encodeURIComponent(query.value)}&limit=5`);
    
    if (response.ok) {
      const data = await response.json();
      suggestions.value = data.data?.map(product => product.name) || [];
    }
  } catch (error) {
    console.error('Failed to fetch suggestions:', error);
    suggestions.value = [];
  }
};

const selectSuggestion = (suggestion) => {
  query.value = suggestion;
  emit('update:modelValue', query.value);
  hideSuggestions();
  emit('search', query.value);
};

const selectNext = (event) => {
  event.preventDefault();
  if (suggestions.value.length === 0) return;
  
  selectedIndex.value = selectedIndex.value < suggestions.value.length - 1 
    ? selectedIndex.value + 1 
    : 0;
};

const selectPrev = (event) => {
  event.preventDefault();
  if (suggestions.value.length === 0) return;
  
  selectedIndex.value = selectedIndex.value > 0 
    ? selectedIndex.value - 1 
    : suggestions.value.length - 1;
};

const hideSuggestions = () => {
  showSuggestions.value = false;
  selectedIndex.value = -1;
};

const hideSuggestionsDelayed = () => {
  // Delay to allow click events on suggestions
  setTimeout(() => {
    hideSuggestions();
  }, 200);
};
</script>

<style scoped>
.search-container {
  @apply relative flex;
}

.search-input {
  @apply flex-1 px-4 py-2 pr-12 border border-gray-300 rounded-lg;
  @apply focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
}

.search-button {
  @apply absolute right-2 top-1/2 transform -translate-y-1/2;
  @apply p-1 text-gray-400 hover:text-gray-600;
}

.suggestions-dropdown {
  @apply absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50;
  @apply max-h-60 overflow-y-auto;
}

.suggestion-item {
  @apply flex items-center px-4 py-2 cursor-pointer hover:bg-gray-50;
  @apply border-b border-gray-100 last:border-b-0;
}

.suggestion-active {
  @apply bg-blue-50 text-blue-600;
}
</style>
