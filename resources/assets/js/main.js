
// Import Bootstrap JS
import * as bootstrap from 'bootstrap';

// Expose bootstrap globally so inline blade scripts can use bootstrap.Modal(), etc.
window.bootstrap = bootstrap;

import './custom.js';

// Import SCSS
import '../scss/style.scss';