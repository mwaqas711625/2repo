<?php
declare(strict_types=1);
require_once __DIR__ . '/database/config.php';
/** Generic PDO CRUD operations for validated table and column names. */
class Admin
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	/** Insert a row and return its generated ID, when available. */
	public function create(string $table, array $data): string
	{
		if ($data === []) {
			throw new InvalidArgumentException('Insert data cannot be empty.');
		}

		$columns = [];
		$placeholders = [];
		$params = [];
		foreach ($data as $column => $value) {
			$columns[] = $this->quoteIdentifier((string) $column);
			$placeholder = ':v' . count($params);
			$placeholders[] = $placeholder;
			$params[$placeholder] = $value;
		}

		$sql = 'INSERT INTO ' . $this->quoteIdentifier($table)
			. ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
		$this->pdo->prepare($sql)->execute($params);

		return $this->pdo->lastInsertId();
	}

	/** Fetch rows matching conditions; empty conditions return all rows. */
	public function read(string $table, array $where = [], array $columns = ['*']): array
	{
		if ($columns === []) {
			throw new InvalidArgumentException('At least one column must be selected.');
		}

		$selected = [];
		foreach ($columns as $column) {
			$selected[] = $column === '*' ? '*' : $this->quoteIdentifier((string) $column);
		}

		[$whereSql, $params] = $this->buildWhere($where);
		$sql = 'SELECT ' . implode(', ', $selected) . ' FROM '
			. $this->quoteIdentifier($table) . $whereSql;
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement->fetchAll();
	}

	/** Update matching rows and return the affected-row count. */
	public function update(string $table, array $data, array $where): int
	{
		if ($data === [] || $where === []) {
			throw new InvalidArgumentException('Update data and conditions cannot be empty.');
		}

		$sets = [];
		$params = [];
		foreach ($data as $column => $value) {
			$placeholder = ':v' . count($params);
			$sets[] = $this->quoteIdentifier((string) $column) . ' = ' . $placeholder;
			$params[$placeholder] = $value;
		}

		[$whereSql, $whereParams] = $this->buildWhere($where, 'w');
		$sql = 'UPDATE ' . $this->quoteIdentifier($table) . ' SET '
			. implode(', ', $sets) . $whereSql;
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params + $whereParams);

		return $statement->rowCount();
	}

	/** Delete matching rows and return the affected-row count. */
	public function delete(string $table, array $where): int
	{
		if ($where === []) {
			throw new InvalidArgumentException('Delete conditions cannot be empty.');
		}

		[$whereSql, $params] = $this->buildWhere($where);
		$statement = $this->pdo->prepare(
			'DELETE FROM ' . $this->quoteIdentifier($table) . $whereSql
		);
		$statement->execute($params);

		return $statement->rowCount();
	}

	private function buildWhere(array $conditions, string $prefix = 'w'): array
	{
		if ($conditions === []) {
			return ['', []];
		}

		$parts = [];
		$params = [];
		foreach ($conditions as $column => $value) {
			$quotedColumn = $this->quoteIdentifier((string) $column);
			if ($value === null) {
				$parts[] = $quotedColumn . ' IS NULL';
				continue;
			}

			$placeholder = ':' . $prefix . count($params);
			$parts[] = $quotedColumn . ' = ' . $placeholder;
			$params[$placeholder] = $value;
		}

		return [' WHERE ' . implode(' AND ', $parts), $params];
	}

	private function quoteIdentifier(string $identifier): string
	{
		if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
			throw new InvalidArgumentException('Invalid table or column name.');
		}

		return '`' . $identifier . '`';
	}
}
