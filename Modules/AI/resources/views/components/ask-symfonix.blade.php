@php
    $suggestions = app(\Modules\AI\Services\Assistant\SuggestionService::class)->forUser(auth()->user());
    $user = auth()->user();
    $userName = trim((string) ($user->name ?? ''));
    $firstName = $userName !== '' ? explode(' ', $userName)[0] : __('ai::assistant.you');
    $userAvatar = filled($user->img) ? asset('storage/'.$user->img) : asset('images/avatar.png');
    $userInitial = mb_strtoupper(mb_substr($firstName, 0, 1));
    $routes = [
        'bootstrap' => route('admin.ai.assistant.bootstrap'),
        'store' => route('admin.ai.assistant.conversations.store'),
        'show' => url(route('admin.ai.assistant.conversations.show', ['conversation' => '__ID__'], false)),
        'destroy' => url(route('admin.ai.assistant.conversations.destroy', ['conversation' => '__ID__'], false)),
        'messages' => url(route('admin.ai.assistant.conversations.messages', ['conversation' => '__ID__'], false)),
        'regenerate' => url(route('admin.ai.assistant.conversations.regenerate', ['conversation' => '__ID__'], false)),
    ];
@endphp

<div class="offcanvas offcanvas-end ask-symfonix-offcanvas" tabindex="-1" id="ask-symfonix-drawer" aria-labelledby="ask-symfonix-title">
    <div class="ask-symfonix-header">
        <div class="ask-symfonix-brand">
            <div class="ask-symfonix-orb" aria-hidden="true">
                <i class="bi bi-stars"></i>
            </div>
            <div class="ask-symfonix-brand-copy">
                <h5 class="ask-symfonix-title mb-0" id="ask-symfonix-title">{{ __('ai::assistant.title') }}</h5>
                <div class="ask-symfonix-status" id="ask-symfonix-status" data-state="ready">
                    <span class="ask-symfonix-status-dot"></span>
                    <span class="ask-symfonix-status-text">{{ __('ai::assistant.ready') }}</span>
                </div>
            </div>
        </div>
        <div class="ask-symfonix-toolbar">
            <button type="button" class="ask-symfonix-icon-btn" id="ask-symfonix-history-toggle" title="{{ __('ai::assistant.history') }}" aria-expanded="false" aria-controls="ask-symfonix-history">
                <i class="bi bi-clock-history"></i>
                <span class="ask-symfonix-history-badge d-none" id="ask-symfonix-history-badge"></span>
            </button>
            <button type="button" class="ask-symfonix-icon-btn" id="ask-symfonix-new" title="{{ __('ai::assistant.new_chat') }}">
                <i class="bi bi-plus-lg"></i>
            </button>
            <button type="button" class="ask-symfonix-icon-btn" data-bs-dismiss="offcanvas" aria-label="{{ __('ai::assistant.close') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="ask-symfonix-history d-none" id="ask-symfonix-history" role="listbox" aria-label="{{ __('ai::assistant.history') }}"></div>

    <div class="ask-symfonix-body">
        <div class="ask-symfonix-messages" id="ask-symfonix-messages"></div>
        <div class="ask-symfonix-composer">
            <form id="ask-symfonix-form" class="ask-symfonix-composer-form">
                <label class="visually-hidden" for="ask-symfonix-input">{{ __('ai::assistant.placeholder') }}</label>
                <textarea id="ask-symfonix-input" rows="1" maxlength="4000"
                          placeholder="{{ __('ai::assistant.placeholder') }}" autocomplete="off"></textarea>
                <button type="submit" class="ask-symfonix-send" id="ask-symfonix-send" disabled aria-label="{{ __('ai::assistant.send') }}">
                    <i class="bi bi-arrow-up"></i>
                </button>
            </form>
            <div class="ask-symfonix-composer-hint">{{ __('ai::assistant.composer_hint') }}</div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            window.AskSymfonix = (function () {
                const routes = @json($routes);
                const suggestions = @json($suggestions);
                const user = {
                    name: @json($firstName),
                    avatar: @json($userAvatar),
                    initial: @json($userInitial),
                };
                const i18n = {
                    you: @json(__('ai::assistant.you')),
                    assistant: @json(__('ai::assistant.assistant')),
                    typing: @json(__('ai::assistant.typing')),
                    greeting: @json(__('ai::assistant.greeting', ['name' => $firstName])),
                    emptyTitle: @json(__('ai::assistant.empty_title')),
                    emptyHint: @json(__('ai::assistant.empty_hint')),
                    copy: @json(__('ai::assistant.copy')),
                    copied: @json(__('ai::assistant.copied')),
                    regenerate: @json(__('ai::assistant.regenerate')),
                    history: @json(__('ai::assistant.history')),
                    historyEmpty: @json(__('ai::assistant.history_empty')),
                    deleteChat: @json(__('ai::assistant.clear')),
                    newChat: @json(__('ai::assistant.new_chat')),
                    notConfigured: @json(__('ai::assistant.not_configured')),
                    genericError: @json(__('ai::assistant.errors.generic')),
                    connect: @json(__('ai::assistant.errors.connect')),
                    ready: @json(__('ai::assistant.ready')),
                    offline: @json(__('ai::assistant.offline')),
                    basedOn: @json(__('ai::assistant.based_on')),
                };
                const suggestionIcons = {
                    invoices: 'bi-receipt',
                    overdue_tasks: 'bi-exclamation-circle',
                    overdue_projects: 'bi-kanban',
                    revenue: 'bi-cash-stack',
                    growth: 'bi-graph-up-arrow',
                    customers: 'bi-people',
                    leads: 'bi-person-plus',
                    today: 'bi-sun',
                    visitors: 'bi-bar-chart-line',
                    services: 'bi-bag-check',
                    employees: 'bi-person-badge',
                };
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const messagesEl = document.getElementById('ask-symfonix-messages');
                const historyEl = document.getElementById('ask-symfonix-history');
                const historyToggle = document.getElementById('ask-symfonix-history-toggle');
                const historyBadge = document.getElementById('ask-symfonix-history-badge');
                const statusEl = document.getElementById('ask-symfonix-status');
                const form = document.getElementById('ask-symfonix-form');
                const input = document.getElementById('ask-symfonix-input');
                const sendBtn = document.getElementById('ask-symfonix-send');
                const drawer = document.getElementById('ask-symfonix-drawer');
                let conversationId = null;
                let conversations = [];
                let configured = true;
                let sending = false;

                function url(template, id) {
                    return template.replace('__ID__', encodeURIComponent(id));
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                }

                function renderMarkdown(text) {
                    let html = escapeHtml(text).replace(/```[\s\S]*?```/g, '');
                    html = html.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
                    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                    html = html.replace(/^(?:- |\* )(.+)$/gm, '<li>$1</li>');
                    html = html.replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>');
                    html = html.replace(/\n{2,}/g, '</p><p>');
                    html = html.replace(/\n/g, '<br>');
                    return '<p>' + html + '</p>';
                }

                function request(method, href, body) {
                    const options = {
                        method,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    };
                    if (body !== undefined) {
                        options.headers['Content-Type'] = 'application/json';
                        options.body = JSON.stringify(body);
                    }
                    return fetch(href, options).then(async function (response) {
                        const data = await response.json().catch(function () { return {}; });
                        if (!response.ok && response.status !== 422) {
                            const error = new Error(data.error || i18n.connect);
                            error.payload = data;
                            throw error;
                        }
                        return data;
                    });
                }

                function setStatus(state) {
                    const labels = { ready: i18n.ready, thinking: i18n.typing, offline: i18n.offline };
                    statusEl.dataset.state = state;
                    statusEl.querySelector('.ask-symfonix-status-text').textContent = labels[state] || i18n.ready;
                }

                function resizeComposer() {
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 148) + 'px';
                    sendBtn.disabled = sending || !input.value.trim();
                }

                function suggestionIcon(id) {
                    return suggestionIcons[id] || 'bi-stars';
                }

                function renderEmpty() {
                    const cards = suggestions.slice(0, 6).map(function (item) {
                        return '<button type="button" class="ask-symfonix-suggestion" data-prompt="' + escapeHtml(item.prompt) + '">' +
                            '<span class="ask-symfonix-suggestion-icon"><i class="bi ' + suggestionIcon(item.id) + '"></i></span>' +
                            '<span class="ask-symfonix-suggestion-label">' + escapeHtml(item.label) + '</span>' +
                            '</button>';
                    }).join('');
                    messagesEl.innerHTML =
                        '<div class="ask-symfonix-empty">' +
                            '<div class="ask-symfonix-orb ask-symfonix-orb--lg" aria-hidden="true"><i class="bi bi-stars"></i></div>' +
                            '<div class="ask-symfonix-empty-greeting">' + escapeHtml(i18n.greeting) + '</div>' +
                            '<div class="ask-symfonix-empty-title">' + escapeHtml(configured ? i18n.emptyTitle : i18n.offline) + '</div>' +
                            '<p class="ask-symfonix-empty-hint">' + escapeHtml(configured ? i18n.emptyHint : i18n.notConfigured) + '</p>' +
                            (configured && cards ? '<div class="ask-symfonix-suggestions">' + cards + '</div>' : '') +
                        '</div>';
                }

                function renderHistory() {
                    const count = conversations.length;
                    historyBadge.textContent = count > 9 ? '9+' : String(count);
                    historyBadge.classList.toggle('d-none', count === 0);

                    if (!count) {
                        historyEl.innerHTML = '<div class="ask-symfonix-history-empty">' + escapeHtml(i18n.historyEmpty) + '</div>';
                        return;
                    }

                    historyEl.innerHTML = conversations.map(function (item) {
                        const active = Number(item.id) === Number(conversationId) ? ' is-active' : '';
                        return '<div class="ask-symfonix-history-item' + active + '" role="option" aria-selected="' + (active ? 'true' : 'false') + '">' +
                            '<button type="button" class="ask-symfonix-history-open" data-id="' + item.id + '">' +
                                '<i class="bi bi-chat-dots"></i>' +
                                '<span>' + escapeHtml(item.title || i18n.emptyTitle) + '</span>' +
                            '</button>' +
                            '<button type="button" class="ask-symfonix-history-delete" data-delete-id="' + item.id + '" title="' + escapeHtml(i18n.deleteChat) + '" aria-label="' + escapeHtml(i18n.deleteChat) + '">' +
                                '<i class="bi bi-trash"></i>' +
                            '</button>' +
                        '</div>';
                    }).join('');
                }

                function sourceChips(sources) {
                    if (!sources || !sources.length) return '';
                    return '<div class="ask-symfonix-sources">' + sources.map(function (source) {
                        return '<span class="ask-symfonix-source">' + escapeHtml(source) + '</span>';
                    }).join('') + '</div>';
                }

                function actionsHtml(isAssistant) {
                    if (!isAssistant) return '';
                    return '<div class="ask-symfonix-actions">' +
                        '<button type="button" class="ask-symfonix-action ask-copy" title="' + escapeHtml(i18n.copy) + '" aria-label="' + escapeHtml(i18n.copy) + '"><i class="bi bi-clipboard"></i></button>' +
                        '<button type="button" class="ask-symfonix-action ask-regen" title="' + escapeHtml(i18n.regenerate) + '" aria-label="' + escapeHtml(i18n.regenerate) + '"><i class="bi bi-arrow-repeat"></i></button>' +
                        '</div>';
                }

                function userAvatarHtml() {
                    if (user.avatar) {
                        return '<img src="' + escapeHtml(user.avatar) + '" alt="" class="ask-symfonix-avatar-img">';
                    }
                    return escapeHtml(user.initial || 'U');
                }

                function appendMessage(message) {
                    const isUser = message.role === 'user';
                    const row = document.createElement('div');
                    row.className = 'ask-symfonix-row ask-symfonix-row--' + (isUser ? 'user' : 'assistant');
                    const bubble = document.createElement('div');
                    bubble.className = 'ask-symfonix-bubble ask-symfonix-bubble--' + (isUser ? 'user' : 'assistant');
                    bubble.dataset.raw = message.content || '';
                    bubble.innerHTML =
                        '<div class="ask-symfonix-avatar" aria-hidden="true">' +
                            (isUser ? userAvatarHtml() : '<i class="bi bi-stars"></i>') +
                        '</div>' +
                        '<div class="ask-symfonix-bubble-body">' +
                            '<div class="ask-symfonix-meta">' + escapeHtml(isUser ? i18n.you : i18n.assistant) + '</div>' +
                            '<div class="ask-symfonix-bubble-text">' +
                                (isUser ? escapeHtml(message.content || '') : renderMarkdown(message.content || '')) +
                            '</div>' +
                            sourceChips(message.sources || []) +
                            actionsHtml(!isUser) +
                        '</div>';
                    row.appendChild(bubble);
                    messagesEl.appendChild(row);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                function showTyping(on) {
                    const existing = document.getElementById('ask-symfonix-typing');
                    if (existing) existing.remove();
                    if (!on) {
                        setStatus(configured ? 'ready' : 'offline');
                        return;
                    }
                    setStatus('thinking');
                    const row = document.createElement('div');
                    row.id = 'ask-symfonix-typing';
                    row.className = 'ask-symfonix-row ask-symfonix-row--assistant';
                    row.innerHTML =
                        '<div class="ask-symfonix-bubble ask-symfonix-bubble--assistant ask-symfonix-bubble--typing">' +
                            '<div class="ask-symfonix-avatar" aria-hidden="true"><i class="bi bi-stars"></i></div>' +
                            '<div class="ask-symfonix-bubble-body">' +
                                '<div class="ask-symfonix-meta">' + escapeHtml(i18n.assistant) + '</div>' +
                                '<div class="ask-symfonix-typing" aria-label="' + escapeHtml(i18n.typing) + '">' +
                                    '<span></span><span></span><span></span>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    messagesEl.appendChild(row);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                function renderMessages(list) {
                    messagesEl.innerHTML = '';
                    if (!list || !list.length) {
                        renderEmpty();
                        return;
                    }
                    list.forEach(appendMessage);
                }

                function setBusy(on) {
                    sending = on;
                    input.disabled = on;
                    sendBtn.disabled = on || !input.value.trim();
                    form.classList.toggle('is-busy', on);
                }

                function ensureConversation() {
                    if (conversationId) {
                        return Promise.resolve(conversationId);
                    }
                    return request('POST', routes.store).then(function (data) {
                        conversationId = data.conversation.id;
                        conversations.unshift({
                            id: conversationId,
                            title: data.conversation.title,
                        });
                        renderHistory();
                        return conversationId;
                    });
                }

                function loadConversation(id) {
                    return request('GET', url(routes.show, id)).then(function (data) {
                        conversationId = data.conversation.id;
                        renderMessages(data.conversation.messages);
                        renderHistory();
                        closeHistory();
                    });
                }

                function deleteConversation(id) {
                    return request('DELETE', url(routes.destroy, id)).then(function () {
                        conversations = conversations.filter(function (item) {
                            return Number(item.id) !== Number(id);
                        });
                        if (Number(conversationId) === Number(id)) {
                            conversationId = null;
                            renderEmpty();
                        }
                        renderHistory();
                    });
                }

                function send(text) {
                    const value = (text || '').trim();
                    if (!value || sending) return;
                    if (!configured) {
                        toastr.error(i18n.notConfigured);
                        return;
                    }
                    if (!messagesEl.querySelector('.ask-symfonix-bubble')) {
                        messagesEl.innerHTML = '';
                    }
                    appendMessage({ role: 'user', content: value });
                    input.value = '';
                    resizeComposer();
                    setBusy(true);
                    showTyping(true);
                    ensureConversation().then(function (id) {
                        return request('POST', url(routes.messages, id), { message: value });
                    }).then(function (data) {
                        showTyping(false);
                        if (data.conversation && data.conversation.messages) {
                            renderMessages(data.conversation.messages);
                            conversationId = data.conversation.id;
                            const item = conversations.find(function (row) { return Number(row.id) === Number(conversationId); });
                            if (item) item.title = data.conversation.title;
                            renderHistory();
                        } else if (data.message) {
                            appendMessage(data.message);
                        }
                        if (!data.success && data.error) {
                            toastr.error(data.error);
                        }
                    }).catch(function () {
                        showTyping(false);
                        appendMessage({ role: 'assistant', content: i18n.connect });
                    }).finally(function () {
                        setBusy(false);
                        input.focus();
                    });
                }

                function open(prompt) {
                    const instance = bootstrap.Offcanvas.getOrCreateInstance(drawer);
                    instance.show();
                    if (prompt) {
                        setTimeout(function () { send(prompt); }, 220);
                    }
                }

                function closeHistory() {
                    historyEl.classList.add('d-none');
                    historyToggle.setAttribute('aria-expanded', 'false');
                    historyToggle.classList.remove('is-active');
                }

                function bootstrapState() {
                    request('GET', routes.bootstrap).then(function (data) {
                        configured = !!data.configured;
                        conversations = data.conversations || [];
                        setStatus(configured ? 'ready' : 'offline');
                        renderHistory();
                        if (!configured) renderEmpty();
                    }).catch(function () {
                        configured = false;
                        setStatus('offline');
                        renderEmpty();
                    });
                }

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    send(input.value);
                });
                input.addEventListener('input', resizeComposer);
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        send(input.value);
                    }
                });
                messagesEl.addEventListener('click', function (event) {
                    const promptBtn = event.target.closest('[data-prompt]');
                    if (promptBtn) {
                        send(promptBtn.getAttribute('data-prompt'));
                        return;
                    }
                    const bubble = event.target.closest('.ask-symfonix-bubble--assistant');
                    if (!bubble) return;
                    if (event.target.closest('.ask-copy')) {
                        const button = event.target.closest('.ask-copy');
                        navigator.clipboard.writeText(bubble.dataset.raw || '').then(function () {
                            button.innerHTML = '<i class="bi bi-check2"></i>';
                            button.classList.add('is-done');
                            toastr.success(i18n.copied);
                            setTimeout(function () {
                                button.innerHTML = '<i class="bi bi-clipboard"></i>';
                                button.classList.remove('is-done');
                            }, 1600);
                        });
                    }
                    if (event.target.closest('.ask-regen') && conversationId && !sending) {
                        setBusy(true);
                        showTyping(true);
                        request('POST', url(routes.regenerate, conversationId)).then(function (data) {
                            showTyping(false);
                            renderMessages(data.conversation.messages);
                        }).catch(function () {
                            showTyping(false);
                            toastr.error(i18n.connect);
                        }).finally(function () {
                            setBusy(false);
                        });
                    }
                });
                document.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-ask-prompt]');
                    if (!button || button.closest('#ask-symfonix-drawer')) return;
                    open(button.getAttribute('data-ask-prompt'));
                });
                historyEl.addEventListener('click', function (event) {
                    const del = event.target.closest('[data-delete-id]');
                    if (del) {
                        event.preventDefault();
                        deleteConversation(del.getAttribute('data-delete-id'));
                        return;
                    }
                    const openBtn = event.target.closest('[data-id]');
                    if (openBtn) {
                        loadConversation(openBtn.getAttribute('data-id'));
                    }
                });
                historyToggle.addEventListener('click', function () {
                    const openPanel = historyEl.classList.contains('d-none');
                    historyEl.classList.toggle('d-none', !openPanel);
                    historyToggle.setAttribute('aria-expanded', openPanel ? 'true' : 'false');
                    historyToggle.classList.toggle('is-active', openPanel);
                });
                document.getElementById('ask-symfonix-new').addEventListener('click', function () {
                    conversationId = null;
                    closeHistory();
                    renderEmpty();
                    input.focus();
                });
                drawer.addEventListener('shown.bs.offcanvas', function () {
                    resizeComposer();
                    input.focus();
                });

                renderEmpty();
                resizeComposer();
                bootstrapState();

                return { open: open, send: send };
            })();
        </script>
    @endpush
@endonce
