// Клиентский скрипт AJAX для переключения счётчика Like без перезагрузки страницы
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.like-btn');
    const banner = document.getElementById('status-banner');

    function showError(msg) {
        banner.textContent = msg;
        banner.hidden = false;
    }

    function hideError() {
        banner.hidden = true;
    }

    buttons.forEach((btn) => {
        btn.addEventListener('click', async () => {
            hideError();
            const newsId = Number(btn.dataset.newsId);
            btn.disabled = true;

            try {
                const response = await fetch('api_like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ news_id: newsId })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.error || 'Не удалось обновить счётчик.');
                }

                // Обновляем DOM без перезагрузки страницы
                const countEl = document.getElementById(`like-count-${newsId}`);
                const iconEl = btn.querySelector('.like-icon');
                const labelEl = btn.querySelector('.like-label');

                countEl.textContent = result.likes_count;

                if (result.liked) {
                    btn.classList.add('liked');
                    btn.setAttribute('aria-pressed', 'true');
                    iconEl.textContent = '♥';
                    labelEl.textContent = 'Убрать Like';
                } else {
                    btn.classList.remove('liked');
                    btn.setAttribute('aria-pressed', 'false');
                    iconEl.textContent = '♡';
                    labelEl.textContent = 'Like';
                }
            } catch (err) {
                showError(err.message);
            } finally {
                btn.disabled = false;
            }
        });
    });
});
