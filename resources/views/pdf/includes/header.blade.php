{{-- Header content for PDF documents --}}
@if (isset($ecoleHeader) && $ecoleHeader)
    {!! $ecoleHeader !!}
@elseif (isset($ecole) && $ecole)
    <div style="text-align: center; margin-bottom: 30px; padding: 20px;">
        @if ($ecole->logo_path)
            <img src="{{ public_path('storage/' . $ecole->logo_path) }}" alt="Logo"
                style="max-height: 80px; margin-bottom: 10px;">
        @endif
        <h2 style="margin: 10px 0; color: #003366; font-size: 18pt;">{{ $ecole->libelle_ecole }}</h2>
        @if ($ecole->adresse_ecole)
            <p style="margin: 5px 0; font-size: 10pt;">{{ $ecole->adresse_ecole }}</p>
        @endif
        @if ($ecole->telephone_ecole || $ecole->email_ecole)
            <p style="margin: 5px 0; font-size: 9pt;">
                @if ($ecole->telephone_ecole)
                    Tél: {{ $ecole->telephone_ecole }}
                @endif
                @if ($ecole->telephone_ecole && $ecole->email_ecole)
                    |
                @endif
                @if ($ecole->email_ecole)
                    Email: {{ $ecole->email_ecole }}
                @endif
            </p>
        @endif
    </div>
@endif
