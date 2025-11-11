import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.{jsx,tsx}', { eager: true });
    console.log(pages); 
    return pages[`./pages/${name}.tsx`]?.default || pages[`./pages/${name}.jsx`]?.default;
  },
  setup({ el, App, props }) {
    createRoot(el).render(
      <App {...props} />
  );
  },
});