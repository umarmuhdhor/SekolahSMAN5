<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Modules\AuditLogs\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $modelLabel = 'Audit Log';

    protected static string|\UnitEnum|null $navigationGroup = 'Keamanan';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'audit-logs';

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
