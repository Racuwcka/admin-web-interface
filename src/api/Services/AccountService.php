<?php

namespace api\Services;

use api\Core\Models\Account\Account;
use api\Core\Models\Result;
use api\Core\Models\Role\RoleIdName;
use api\Core\Models\Scope\ScopeList;
use api\Core\Models\User;
use api\Core\Models\UserAuth\UserAuth;
use api\Core\Models\UserAuth\UserInfo;
use api\Core\Models\Warehouse\WarehouseIdName;
use api\Core\Repositories\Account\AccountRepository;
use api\Core\Repositories\Account\DTO\AccountDTO;
use api\Core\Repositories\Role\RoleRepository;
use api\Core\Repositories\Warehouse\WarehouseRepository;
use api\Core\Storage\SessionStorage;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Core\Localizations\Localizations;
use Core\Services\ConvertEncoding;
use Fpdf\FPDF;

class AccountService {

    public static function getAuth(?string $token = null, ?ScopeList $scopeList = null, ?string $bxToken = null): ?UserAuth
    {
        if (!SessionService::getSession($token)) {
            return null;
        }

        if (is_null($bxToken)) {
            $bxToken = AccountRepository::getBxToken(userId: SessionStorage::user()->id, platform: 'mdm');
            if (is_null($bxToken)) {
                return null;
            }
        }

        $scopeList = $scopeList ?: LichiIdService::get($bxToken);
        if (is_null($scopeList)) {
            return null;
        }

        if (!AccountService::updateBitrix(userId: SessionStorage::user()->id, scopeList: $scopeList, token: $bxToken)) {
            return null;
        }

        $accountWarehouse = WarehouseService::get(SessionStorage::user()->warehouse);
        if (is_null($accountWarehouse)) {
            return null;
        }

        if (!$accountWarehouse->consolidated) {
            $sessionWarehouse = $accountWarehouse;
        } else {
            if (!is_null(SessionStorage::warehouseIdOrNull())) {
                $sessionWarehouse = WarehouseService::get(SessionStorage::warehouseIdOrNull());
            } else {
                $sessionWarehouse = null;
            }
        }

        if ($sessionWarehouse !== null) {
            $accessModules = ModuleService::getListAccessModules(
                warehouseId: $sessionWarehouse->id,
                roleId: SessionStorage::user()->role
            );
        }

        return new UserAuth(
            user: new UserInfo(
                id: SessionStorage::user()->id,
                name: UserInfo::getFullName(
                    firstName: $scopeList->info->name,
                    lastName: $scopeList->info->lastName,
                    secondName: $scopeList->info->secondName
                ),
                photo: $scopeList->photo->photo,
                birthday: $scopeList->data->birthday,
                gender: $scopeList->data->gender
            ),
            accountWarehouse: $accountWarehouse,
            sessionWarehouse: $sessionWarehouse,
            accessModules: $accessModules ?? []
        );
    }

    public static function checkRequiredFields(AccountDTO $user): bool
    {
        $fields = array_keys(get_object_vars($user));

        foreach ($fields as $field) {
            if (is_null($user->$field) && ($field == 'auth_id' || $field == 'warehouse' || $field == 'roleId')) {
                return false;
            }
        }
        return true;
    }

    public static function create(ScopeList $scopeList, string $token): bool
    {
        $userDataBase = new AccountDTO(
            auth_id: null,
            bx_id: $scopeList->id->id,
            first_name: $scopeList->info->name,
            last_name: $scopeList->info->lastName,
            second_name: $scopeList->info->secondName,
            photo: $scopeList->photo->photo,
            birthday: $scopeList->data->birthday,
            gender: $scopeList->data->gender,
            roleId: null,
            warehouse: null,
            active: 1
        );

        return AccountRepository::create(user: $userDataBase, bxToken: $token, platform: 'mdm');
    }

    public static function updateBitrix(int $userId, ScopeList $scopeList, string $token): bool
    {
        return AccountRepository::updateBitrixData(
            id: $userId,
            bxToken: $token,
            bxId: $scopeList->id->id,
            firstName: $scopeList->info->name,
            lastName: $scopeList->info->lastName,
            secondName: $scopeList->info->secondName,
            photo: $scopeList->photo->photo,
            birthday: $scopeList->data->birthday,
            gender: $scopeList->data->gender,
            platform: 'mdm'
        );
    }
    /**
     * Получение списка аккаунтов
     * @return array<Account>
     */
    public static function getList(?int $limit, ?int $page): array
    {
        $accountListDTO = AccountRepository::getList(limit: $limit, page: $page);
        return self::getListAccountFromDTO($accountListDTO);
    }

    /**
     * Поиск пользователей по имени
     * @param string $name
     * @return array<Account>
     */
    public static function search(string $name): array
    {
        $names = explode(' ', $name);
        $accountListDTO = AccountRepository::search($names);
        return self::getListAccountFromDTO($accountListDTO);
    }

    /** Получение количества аккаунтов */
    public static function getCount(): int
    {
        return AccountRepository::getCount();
    }

    /** Обновление полей warehouse, roleId, active */
    public static function update(int $id, string $warehouse, int $role, int $active): Result
    {
        if (!WarehouseRepository::exists($warehouse)) {
            return Result::error('invalidWarehouse');
        }

        if (!RoleRepository::exists($role)) {
            return Result::error('invalidRole');
        }

        if (!AccountRepository::update(
            id: $id,
            warehouse: $warehouse,
            role: $role,
            active: $active
        )) {
            return Result::error('failed.update.account');
        }
        return Result::do(true);
    }

    public static function generate(int $id): bool
    {
        try {
            $pinCode = rand(1000, 9999);
            $authId = md5($id . time());
            if (!AccountRepository::updateAuthId(authId: md5($authId), id: $id)) {
                return false;
            }

            $hash = EncryptService::encode($authId, md5($pinCode));

            $user = self::get($id);
            if (is_null($user)) {
                return false;
            }

            $renderer = new ImageRenderer(
                new RendererStyle(
                    size: 350,
                ),
                new ImagickImageBackEnd('jpeg')
            );

            $writer = new Writer($renderer);

            if (!file_exists('tmp')) {
                mkdir(ROOT_DIRECTORY . '/tmp');
            }

            $qr = 'tmp/qrcodeAccount.jpeg';
            $writer->writeFile($hash, $qr);

            header("Access-Control-Expose-Headers: Content-Disposition");

            $pdf = new FPDF("L", "pt", [400 ,700]);

            $pdf->AddPage();
            $pdf->AddFont('arial-amu','','arial-amu.php');
            $pdf->AddFont('arial-black','','arial-black.php');
            $pdf->Image($qr, 25, 25, 350);
            unlink($qr);

            $pdf->SetXY(400, 80);
            $pdf->SetFont('arial-black','', 24);

            $pdf->MultiCell(280, 30, ConvertEncoding::cp1251($user->name), "0", "C");
            $pdf->SetXY(400, 180);

            $pdf->SetFont('arial-amu','', 22);
            $pdf->MultiCell(280, 30, ConvertEncoding::cp1251(WarehouseService::getName($user->warehouse)), "0", "C");
            $pdf->SetXY(400, 280);

            $pdf->SetFont('arial-amu','', 20);
            $pdf->MultiCell(280, 30, ConvertEncoding::cp1251(Localizations::get('code.access.account')), "0", "C");
            $pdf->Output("D", $user->name.'_'.$pinCode.'.pdf', true);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function get(int $id): ?User
    {
        $userDTO = AccountRepository::getById($id);
        if (is_null($userDTO)) {
            return null;
        }
        return User::fromDTO($userDTO);
    }

    /**
     * @param array<AccountDTO> $data
     * @return array<Account>
     */
    private static function getListAccountFromDTO(array $data): array
    {
        $list = [];
        foreach ($data as $item) {
            $role = null;
            $warehouse = null;

            if (empty($item->first_name) ||
                empty($item->last_name)) {
                continue;
            }

            if (!is_null($item->roleId)) {
                $role = RoleService::getName($item->roleId);
            }

            if (!is_null($item->warehouse)) {
                $warehouse = WarehouseService::getName($item->warehouse);
            }

            $list[] = new Account(
                id: $item->id,
                firstName: $item->first_name,
                lastName: $item->last_name,
                secondName: $item->second_name ?? '',
                photo: $item->photo,
                birthday: $item->birthday,
                gender: $item->gender,
                role: is_null($role) ? $role : new RoleIdName(
                    id: $item->roleId,
                    name: $role
                ),
                warehouse: is_null($warehouse) ? $warehouse : new WarehouseIdName(
                    id: $item->warehouse,
                    name: $warehouse
                ),
                active: boolval($item->active)
            );
        }

        return $list;
    }
}