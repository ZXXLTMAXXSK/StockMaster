// Efecto líquido con movimiento rápido y colores vivos
(() => {
  const sec = document.getElementById('sec1');
  const layers = Array.from(sec.querySelectorAll('.liquid-layer'));

  // Parámetros: mayor velocidad y amplitud
  const params = [
    { speed: 0.0022, amp: 120, rot: 0.0005 },
    { speed: 0.0018, amp: 140, rot: -0.0004 },
    { speed: 0.0026, amp: 100, rot: 0.0006 },
  ];

  let start = performance.now();

  function animate(now) {
    const t = now - start;

    layers.forEach((el, i) => {
      const p = params[i];
      const x = Math.sin(t * p.speed * (i + 1)) * p.amp;
      const y = Math.cos(t * p.speed * (i + 1.2)) * p.amp * 0.6;
      const s = 1 + Math.sin(t * p.speed * 0.5 + i) * 0.05;
      const r = Math.sin(t * p.rot + i) * 10;

      el.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${s}) rotate(${r}deg)`;

      // Efecto arcoíris más rápido
      const hue = (t * 0.06 + i * 80) % 360;
      el.style.setProperty(`--c${i + 1}`, `hsla(${hue}, 90%, 60%, 0.95)`);
    });

    requestAnimationFrame(animate);
  }

  requestAnimationFrame(animate);
})();
