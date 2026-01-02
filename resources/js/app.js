import '../sass/app.scss';
import './config';

document.addEventListener('DOMContentLoaded', () => {
  const trigger = document.querySelector('[data-modal-trigger]');
  const backdrop = document.querySelector('[data-modal]');
  const closeButtons = document.querySelectorAll('[data-modal-close]');

  if (trigger && backdrop) {
    trigger.addEventListener('click', () => {
      backdrop.classList.add('open');
    });
  }

  closeButtons.forEach((btn) =>
    btn.addEventListener('click', () => {
      backdrop?.classList.remove('open');
    })
  );
});
