document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.checks').forEach((group) => {
    group.addEventListener('change', () => group.classList.add('touched'));
  });
});
