import '../sass/app.scss';
import './config';

document.addEventListener('DOMContentLoaded', () => {
  const trigger = document.querySelector('[data-modal-trigger]');
  const backdrop = document.querySelector('[data-modal]');
  const closeButtons = document.querySelectorAll('[data-modal-close]');
  const tabContainers = document.querySelectorAll('[data-tabs]');

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

  tabContainers.forEach((tabs) => {
    const buttons = tabs.querySelectorAll('[data-tab-target]');
    const panels = tabs.querySelectorAll('[data-tab-panel]');

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const target = button.dataset.tabTarget;

        buttons.forEach((btn) => btn.classList.toggle('is-active', btn === button));
        panels.forEach((panel) =>
          panel.classList.toggle('is-active', panel.id === target)
        );
      });
    });
  });
});
