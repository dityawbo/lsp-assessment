<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Search, Filter, BookOpen, Award, Calendar, ChevronUp } from 'lucide-vue-next';

// Add missing variables
const mobileFilterOpen = ref(false);
const sortOrder = ref('newest');
const windowWidth = ref(window.innerWidth);

// Dummy data for certification schemes based on database schema
// Remove isNew and isPopular properties from all scheme objects
const schemes = [
    {
        id: 1,
        scheme_code: 'KKNI-NA-001',
        scheme_name: 'Network Administrator Muda',
        scheme_type: 'KKNI',
        competency_units: 5,
        scheme_document_path: '/documents/schemes/network-admin.pdf',
        scheme_summary: 'Skema sertifikasi untuk profesional jaringan tingkat pemula yang mencakup konfigurasi, pemeliharaan, dan troubleshooting jaringan komputer.',
        image: '/images/schemes/network-admin.png',
        created_at: '2023-10-15T08:30:00Z'
    },
    {
        id: 2,
        scheme_code: 'KKNI-BD-002',
        scheme_name: 'Ilmuwan Big Data',
        scheme_type: 'KKNI',
        competency_units: 6,
        scheme_document_path: '/documents/schemes/big-data.pdf',
        scheme_summary: 'Skema sertifikasi untuk profesional data science yang fokus pada pengolahan dan analisis big data menggunakan berbagai tools dan metodologi.',
        image: '/images/schemes/big-data.png',
        created_at: '2023-09-20T10:15:00Z'
    },
    {
        id: 3,
        scheme_code: 'KKNI-DB-003',
        scheme_name: 'Pemrogram Basisdata',
        scheme_type: 'KKNI',
        competency_units: 4,
        scheme_document_path: '/documents/schemes/database.pdf',
        scheme_summary: 'Skema sertifikasi untuk pengembang database yang mencakup desain, implementasi, dan optimasi database relasional dan non-relasional.',
        image: '/images/schemes/database.png',
        created_at: '2023-08-05T14:45:00Z'
    },
    {
        id: 4,
        scheme_code: 'KKNI-MM-004',
        scheme_name: 'Desainer Multimedia Muda',
        scheme_type: 'KKNI',
        competency_units: 5,
        scheme_document_path: '/documents/schemes/multimedia.pdf',
        scheme_summary: 'Skema sertifikasi untuk desainer multimedia yang mencakup desain grafis, animasi, dan pengembangan konten interaktif.',
        image: '/images/schemes/multimedia.png',
        created_at: '2023-07-12T09:30:00Z'
    },
    {
        id: 5,
        scheme_code: 'OKP-DM-005',
        scheme_name: 'Pemasaran Digital',
        scheme_type: 'Okupasi',
        competency_units: 4,
        scheme_document_path: '/documents/schemes/digital-marketing.pdf',
        scheme_summary: 'Skema sertifikasi untuk spesialis pemasaran digital yang mencakup SEO, SEM, media sosial, dan analitik digital.',
        image: '/images/schemes/digital-marketing.png',
        created_at: '2023-06-18T11:20:00Z'
    },
    {
        id: 6,
        scheme_code: 'OKP-PM-006',
        scheme_name: 'Manajer Proyek',
        scheme_type: 'Okupasi',
        competency_units: 7,
        scheme_document_path: '/documents/schemes/project-manager.pdf',
        scheme_summary: 'Skema sertifikasi untuk manajer proyek yang mencakup perencanaan, eksekusi, monitoring, dan penutupan proyek teknologi informasi.',
        image: '/images/schemes/project-manager.png',
        created_at: '2023-05-25T13:40:00Z'
    },
    {
        id: 7,
        scheme_code: 'KLS-IT-007',
        scheme_name: 'Auditor IT',
        scheme_type: 'Klaster',
        competency_units: 5,
        scheme_document_path: '/documents/schemes/it-audit.pdf',
        scheme_summary: 'Skema sertifikasi untuk auditor IT yang mencakup evaluasi keamanan, kepatuhan, dan efektivitas sistem informasi.',
        image: '/images/schemes/it-audit.png',
        created_at: '2023-04-10T15:50:00Z'
    },
    {
        id: 8,
        scheme_code: 'KLS-NT-008',
        scheme_name: 'Teknisi Jaringan',
        scheme_type: 'Klaster',
        competency_units: 3,
        scheme_document_path: '/documents/schemes/network-tech.pdf',
        scheme_summary: 'Skema sertifikasi untuk teknisi jaringan yang mencakup instalasi, konfigurasi, dan pemeliharaan infrastruktur jaringan.',
        image: '/images/schemes/network-tech.png',
        created_at: '2023-03-15T10:30:00Z'
    }
];

// Update categories to remove any references to 'popular' and 'new'
const categories = [
    { id: 'all', name: 'Semua Skema' },
    { id: 'okupasi', name: 'Okupasi', dbValue: 'Okupasi' },
    { id: 'kkni', name: 'KKNI', dbValue: 'KKNI' },
    { id: 'klaster', name: 'Klaster', dbValue: 'Klaster' },
    { id: 'digital-marketing', name: 'Digital Marketing & Office' },
    { id: 'data-science', name: 'Data Science' },
    { id: 'software-dev', name: 'Software Development' },
    { id: 'multimedia', name: 'Multimedia' },
    { id: 'project-quality', name: 'Project Quality & Management' },
    { id: 'network-admin', name: 'Network Administrator' },
    { id: 'graphic-design', name: 'Graphic Design' }
];

const selectedCategory = ref('all');
const searchQuery = ref('');

// Filter schemes based on selected category and search query
const filteredSchemes = computed(() => {
    let filtered = schemes;
    
    // Filter by category - only keep the valid filters
    if (['okupasi', 'kkni', 'klaster'].includes(selectedCategory.value)) {
        // Filter by scheme_type
        const categoryObj = categories.find(cat => cat.id === selectedCategory.value);
        filtered = filtered.filter(scheme => 
            scheme.scheme_type === categoryObj?.dbValue
        );
    } else if (selectedCategory.value !== 'all') {
        // Filter by other categories (more generic)
        const categoryName = categories.find(cat => cat.id === selectedCategory.value)?.name;
        filtered = filtered.filter(scheme => 
            scheme.scheme_name.includes(categoryName || '') || 
            scheme.scheme_summary.includes(categoryName || '')
        );
    }
    
    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(scheme => 
            scheme.scheme_name.toLowerCase().includes(query) || 
            scheme.scheme_code.toLowerCase().includes(query) ||
            scheme.scheme_summary.toLowerCase().includes(query)
        );
    }
    
    return filtered;
});

const selectCategory = (categoryId: string) => {
    selectedCategory.value = categoryId;
};

// Back to top functionality
const showBackToTop = ref(false);

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500;
};

// Update the window width on resize
const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
    if (windowWidth.value >= 768) {
        mobileFilterOpen.value = true;
    }
};

// Sort the schemes based on the selected sort order
const sortedSchemes = computed(() => {
    return [...filteredSchemes.value].sort((a, b) => 
        new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    );
});

// Update the onMounted and onUnmounted hooks
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', updateWindowWidth);
    updateWindowWidth(); // Initialize on mount
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', updateWindowWidth);
});
</script>

<template>
    <Head title="Skema Sertifikasi" />
    
    <Navbar />
    
    <div class="bg-gradient-to-r from-cyan-700 to-blue-900 text-white py-8 md:py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 md:mb-4">Skema Sertifikasi</h1>
            <p class="text-base md:text-lg max-w-2xl opacity-90">Temukan berbagai skema sertifikasi profesional untuk meningkatkan kompetensi dan karir Anda di bidang teknologi informasi.</p>
        </div>
    </div>
    
    <main class="container mx-auto px-4 py-6 md:py-8">
        <!-- Mobile filter button (visible only on small screens) -->
        <div class="md:hidden mb-4">
            <Button 
                @click="mobileFilterOpen = !mobileFilterOpen" 
                variant="outline" 
                class="w-full flex items-center justify-center"
            >
                <Filter class="h-4 w-4 mr-2" />
                {{ mobileFilterOpen ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}
            </Button>
        </div>
        
        <div class="flex flex-col md:flex-row gap-6 md:gap-8">
            <!-- Sidebar with filters - conditionally shown on mobile -->
            <div 
                class="w-full md:w-1/4" 
                :class="{ 'hidden': !mobileFilterOpen }"
                v-show="mobileFilterOpen || windowWidth >= 768"
            >
                <div class="bg-white rounded-lg shadow-md p-5 sticky top-24">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <Filter class="h-5 w-5 mr-2 text-cyan-600" />
                        Filter Skema
                    </h2>
                    
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-3 uppercase tracking-wider">Filter By</h3>
                        <ul class="space-y-2">
                            <li v-for="cat in categories.slice(0, 1)" :key="cat.id" 
                                :class="[
                                    'cursor-pointer px-3 py-2 rounded-md transition-colors flex items-center',
                                    selectedCategory === cat.id 
                                        ? 'bg-cyan-50 text-cyan-700 font-medium' 
                                        : 'hover:bg-gray-50 hover:text-cyan-600'
                                ]"
                                @click="selectCategory(cat.id)">
                                <span v-if="cat.id === 'all'" class="mr-2">🔍</span>
                                {{ cat.name }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-3 uppercase tracking-wider">Kategori</h3>
                        <ul class="space-y-1">
                            <li v-for="cat in categories.slice(3, 6)" :key="cat.id" 
                                :class="[
                                    'cursor-pointer px-3 py-2 rounded-md transition-colors',
                                    selectedCategory === cat.id 
                                        ? 'bg-cyan-50 text-cyan-700 font-medium' 
                                        : 'hover:bg-gray-50 hover:text-cyan-600'
                                ]"
                                @click="selectCategory(cat.id)">
                                {{ cat.name }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-3 uppercase tracking-wider">Bidang Keahlian</h3>
                        <ul class="space-y-1">
                            <li v-for="cat in categories.slice(6)" :key="cat.id" 
                                :class="[
                                    'cursor-pointer px-3 py-2 rounded-md transition-colors',
                                    selectedCategory === cat.id 
                                        ? 'bg-cyan-50 text-cyan-700 font-medium' 
                                        : 'hover:bg-gray-50 hover:text-cyan-600'
                                ]"
                                @click="selectCategory(cat.id)">
                                {{ cat.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main content -->
            <div class="w-full md:w-3/4">
                <!-- Search bar -->
                <div class="mb-6 bg-white rounded-lg shadow-md p-4 flex items-center">
                    <Search class="h-5 w-5 text-gray-400 mr-2" />
                    <Input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Cari berdasarkan nama, kode, atau deskripsi skema..." 
                        class="w-full border-0 focus-visible:ring-0 focus-visible:ring-offset-0"
                    />
                </div>
                
                <!-- Category tabs for mobile -->
                <div class="flex overflow-x-auto space-x-2 mb-6 pb-2 md:hidden">
                    <Button 
                        v-for="cat in categories.slice(0, 1)" 
                        :key="cat.id"
                        :variant="selectedCategory === cat.id ? 'default' : 'outline'"
                        :class="selectedCategory === cat.id ? 'bg-cyan-600' : ''"
                        @click="selectCategory(cat.id)"
                        size="sm"
                    >
                        {{ cat.name }}
                    </Button>
                </div>
                
                <!-- Results count with responsive layout -->
                <div class="mb-4 md:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="text-gray-600 mb-2 sm:mb-0">
                        Menampilkan {{ filteredSchemes.length }} skema sertifikasi
                    </div>
                </div>
                
                <!-- Schemes grid - responsive columns -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6">
                    <div v-for="scheme in sortedSchemes" :key="scheme.id" 
                        class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 flex flex-col">
                        <div class="relative">
                            <img :src="scheme.image" :alt="scheme.scheme_name" class="w-full h-40 sm:h-52 object-contain bg-gray-50 p-4" />
                            <!-- Removed the "Terbaru" and "Populer" badges -->
                            <div class="absolute top-3 right-3 bg-gray-800 bg-opacity-80 text-white text-xs font-medium px-3 py-1 rounded-full shadow-sm">
                                {{ scheme.scheme_type }}
                            </div>
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <div class="flex items-center text-xs text-gray-500 mb-2">
                                <span class="bg-cyan-50 text-cyan-700 px-2 py-1 rounded font-medium">{{ scheme.scheme_code }}</span>
                                <span class="ml-auto flex items-center">
                                    <Calendar class="h-3.5 w-3.5 mr-1" />
                                    {{ new Date(scheme.created_at).toLocaleDateString('id-ID') }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ scheme.scheme_name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-grow">{{ scheme.scheme_summary.substring(0, 120) }}...</p>
                            <div class="flex items-center justify-between mb-4 text-sm">
                                <span class="flex items-center text-cyan-700 font-medium">
                                    <BookOpen class="h-4 w-4 mr-1" />
                                    {{ scheme.competency_units }} Unit Kompetensi
                                </span>
                                <span class="flex items-center text-amber-700">
                                    <Award class="h-4 w-4 mr-1" />
                                    Sertifikasi BNSP
                                </span>
                            </div>
                            <Button class="w-full bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white shadow-sm">
                                Lihat Detail Skema
                            </Button>
                        </div>
                    </div>
                </div>
                
                <!-- Empty state -->
                <div v-if="filteredSchemes.length === 0" class="text-center py-16 bg-white rounded-lg shadow-md">
                    <div class="text-gray-400 mb-3">
                        <Search class="h-12 w-12 mx-auto" />
                    </div>
                    <p class="text-gray-600 text-lg font-medium">Tidak ada skema sertifikasi yang ditemukan.</p>
                    <p class="text-gray-500 mt-1">Coba ubah filter atau kata kunci pencarian Anda.</p>
                    <Button @click="selectedCategory = 'all'; searchQuery = ''" variant="outline" class="mt-4">
                        Reset Pencarian
                    </Button>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Back to Top Button -->
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <button
            v-show="showBackToTop"
            @click="scrollToTop"
            class="fixed bottom-6 right-6 bg-cyan-600 hover:bg-cyan-700 text-white rounded-full p-3 shadow-lg z-50 transition-all duration-300 hover:shadow-xl"
            aria-label="Back to top"
        >
            <ChevronUp class="h-6 w-6" />
        </button>
    </transition>
</template>