import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

import Trix from "trix";

window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();