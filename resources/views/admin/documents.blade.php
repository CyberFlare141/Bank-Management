<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

        .admin-docs {
            min-height: 100vh;
            background: #0f1623;
            color: #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 40px 20px 80px;
        }

        .admin-docs-wrap {
            max-width: 1100px;
            margin: 0 auto;
        }

        .admin-docs-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .admin-docs-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #1e2d45;
            background: rgba(15, 22, 35, 0.7);
            color: #cbd5e0;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-docs-btn:hover {
            color: #f8fafc;
            border-color: #334155;
        }

        .admin-docs-title {
            font-size: 30px;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 6px;
        }

        .admin-docs-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .admin-docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
        }

        .admin-doc-card {
            background: #161d2e;
            border: 1px solid #1e2d45;
            border-radius: 16px;
            padding: 18px;
        }

        .admin-doc-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .admin-doc-name {
            font-size: 16px;
            font-weight: 800;
            color: #f8fafc;
        }

        .admin-doc-email {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 13px;
            word-break: break-word;
        }

        .admin-doc-badge {
            border-radius: 999px;
            padding: 6px 10px;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.25);
            color: #7dd3fc;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .admin-doc-photo {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid #1e2d45;
            margin-bottom: 14px;
            background: #0f172a;
        }

        .admin-doc-list {
            display: grid;
            gap: 10px;
        }

        .admin-doc-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #7dd3fc;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .admin-doc-link:hover {
            color: #bae6fd;
        }

        .admin-doc-empty {
            padding: 32px;
            border-radius: 16px;
            border: 1px dashed #334155;
            text-align: center;
            color: #64748b;
            background: rgba(15, 23, 42, 0.4);
        }
    </style>

    <div class="admin-docs">
        <div class="admin-docs-wrap">
            <div class="admin-docs-top">
                <div>
                    <h1 class="admin-docs-title">Registration Documents</h1>
                    <p class="admin-docs-subtitle">Review the files uploaded during user registration from local storage.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="admin-docs-btn">Back To Dashboard</a>
            </div>

            @if ($documents->isEmpty())
                <div class="admin-doc-empty">No uploaded user documents were found.</div>
            @else
                <div class="admin-docs-grid">
                    @foreach ($documents as $document)
                        <article class="admin-doc-card">
                            <div class="admin-doc-head">
                                <div>
                                    <div class="admin-doc-name">{{ $document->user?->name ?? 'Unknown User' }}</div>
                                    <div class="admin-doc-email">{{ $document->user?->email ?? 'No email found' }}</div>
                                </div>
                                <div class="admin-doc-badge">{{ ucfirst($document->account_type) }}</div>
                            </div>

                            @if ($document->photo)
                                <a href="{{ route('admin.user-documents.show', [$document, 'photo']) }}" target="_blank" rel="noopener">
                                    <img src="{{ route('admin.user-documents.show', [$document, 'photo']) }}" alt="User photo" class="admin-doc-photo">
                                </a>
                            @endif

                            <div class="admin-doc-list">
                                <a class="admin-doc-link" href="{{ route('admin.user-documents.show', [$document, 'nid_or_birth_certificate']) }}" target="_blank" rel="noopener">NID / Birth Certificate</a>
                                @if ($document->job_id)
                                    <a class="admin-doc-link" href="{{ route('admin.user-documents.show', [$document, 'job_id']) }}" target="_blank" rel="noopener">Job ID</a>
                                @endif
                                @if ($document->student_id)
                                    <a class="admin-doc-link" href="{{ route('admin.user-documents.show', [$document, 'student_id']) }}" target="_blank" rel="noopener">Student ID</a>
                                @endif
                                <a class="admin-doc-link" href="{{ route('admin.user-documents.show', [$document, 'electric_bill']) }}" target="_blank" rel="noopener">Electric Bill</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
