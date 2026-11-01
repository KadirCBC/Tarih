/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Antik Taş & Mermer Arka Plan Tonları
        stone: {
          950: '#0B0F17', // Derin Obsidyen Taş
          900: '#111622', // Koyu Bazalt / Mermer
          850: '#171E2E',
          800: '#1F293D',
        },
        slate: {
          950: '#080C14',
          900: '#0F172A',
        },
        // Vurgu Renkleri: Antik Altın (Gold)
        gold: {
          antique: '#D4AF37', // Klasik Antik Altın
          light: '#F3E5AB',
          hover: '#E5C158',
          burnished: '#B8860B', // Mat Yanık Altın
          dark: '#996515',
        },
        // Vurgu Renkleri: Eski Gümüş (Silver)
        silver: {
          antique: '#C0C0C0', // Roma Denarius Gümüşü
          patina: '#94A3B8',
          light: '#E2E8F0',
          dark: '#64748B',
        },
        // Vurgu Renkleri: Bronz & Patina Yeşili (Bronze & Patina)
        patina: {
          green: '#2E8B57', // Deniz Yeşili / Antik Sikke Patinası
          dark: '#1B4D3E',
          teal: '#164E63',
          light: '#3CB371',
        },
        bronze: {
          antique: '#8B5A2B', // Antik Sestertius / As Bronzu
          deep: '#5C3A21',
          dark: '#3D2314',
          accent: '#A0522D',
        },
      },
      fontFamily: {
        // Başlıklar ve Latince/Grekçe Lejantlar için Tırnaklı (Serif)
        numis: ['Cinzel', 'Playfair Display', 'serif'],
        serif: ['Cinzel', 'Playfair Display', 'Georgia', 'serif'],
        decorative: ['Cinzel Decorative', 'serif'],
        // Gövde Metinleri için Modern Sans-Serif
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      boxShadow: {
        'numis-gold': '0 0 25px -5px rgba(212, 175, 55, 0.25)',
        'numis-patina': '0 0 25px -5px rgba(46, 139, 87, 0.25)',
        'stone-inset': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.6)',
      },
      backgroundImage: {
        'marble-pattern': "radial-gradient(circle at 50% 0%, rgba(212, 175, 55, 0.08) 0%, transparent 75%)",
        'stone-gradient': "linear-gradient(180deg, #111622 0%, #0B0F17 100%)",
      },
    },
  },
  plugins: [],
}

