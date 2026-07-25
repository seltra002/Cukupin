/** Design tokens brand Cukupin */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        ink: '#1C2620',
        'ink-soft': '#4B5A50',
        paper: '#EEF0E4',
        cream: '#FBF9F2',
        pine: '#1F4B3F',
        'pine-dark': '#163730',
        kunyit: '#E8A23C',
        'kunyit-dark': '#C67F1F',
        chili: '#C1442D',
        sprout: '#6F9955',
        line: 'rgba(28,38,32,0.14)',
      },
      fontFamily: {
        display: ['Fraunces', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
};
