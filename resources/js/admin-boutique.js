const toggle = document.querySelector('.ht-admin-toggle');
const backdrop = document.querySelector('.ht-admin-backdrop');
const sidebar = document.getElementById('admin-sidebar');
function setMenu(open) {
    document.body.classList.toggle('admin-menu-open', open);
    toggle?.setAttribute('aria-expanded', String(open));
    toggle?.setAttribute('aria-label', open ? 'Đóng menu quản trị' : 'Mở menu quản trị');
    if (backdrop) backdrop.hidden = !open;
    if (sidebar) sidebar.inert = !open && window.innerWidth <= 991;
}
toggle?.addEventListener('click', () => setMenu(!document.body.classList.contains('admin-menu-open')));
backdrop?.addEventListener('click', () => { setMenu(false); toggle?.focus(); });
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && document.body.classList.contains('admin-menu-open')) {
        setMenu(false);
        toggle?.focus();
    }
});
window.addEventListener('resize', () => setMenu(false));
setMenu(false);
